<?php

namespace LeoVie\PhpMethodModifier\Tests\Unit\Service;

use LeoVie\PhpMethodModifier\Exception\MethodCannotBeModifiedToNonClassContext;
use LeoVie\PhpMethodModifier\Model\AccessModifier\AccessModifierCollection;
use LeoVie\PhpMethodModifier\Model\AccessModifier\PrivateModifier;
use LeoVie\PhpMethodModifier\Model\AccessModifier\ProtectedModifier;
use LeoVie\PhpMethodModifier\Model\AccessModifier\PublicModifier;
use LeoVie\PhpMethodModifier\Model\Method;
use LeoVie\PhpMethodModifier\Model\MethodContext\ClassMethodContext;
use LeoVie\PhpMethodModifier\Model\MethodContext\FreeMethodContext;
use LeoVie\PhpMethodModifier\Service\MethodModifierService;
use PHPUnit\Framework\TestCase;

class MethodModifierServiceTest extends TestCase
{
    private MethodModifierService $methodModifierService;

    protected function setUp(): void
    {
        $accessModifierCollection = new AccessModifierCollection(new \ArrayIterator([
            PublicModifier::create(),
            ProtectedModifier::create(),
            PrivateModifier::create(),
        ]));

        $this->methodModifierService = new MethodModifierService($accessModifierCollection);
    }

    /** @dataProvider buildMethodProvider */
    public function testBuildMethod(Method $expected, string $code): void
    {
        self::assertEquals($expected, $this->methodModifierService->buildMethod($code));
    }

    public function buildMethodProvider(): \Generator
    {
        $code = 'function foo(): void { // doSomething }';
        yield 'free method' => [
            'expected' => Method::create($code, FreeMethodContext::create()),
            $code,
        ];

        $code = 'public function foo(): void { // doSomething }';
        yield 'public class method' => [
            'expected' => Method::create($code, ClassMethodContext::create(PublicModifier::create())),
            'code' => ' ' . $code,
        ];

        $code = 'protected function foo(): void { // doSomething }';
        yield 'protected class method' => [
            'expected' => Method::create($code, ClassMethodContext::create(ProtectedModifier::create())),
            'code' => $code,
        ];

        $code = 'private function foo(): void { // doSomething }';
        yield 'private class method' => [
            'expected' => Method::create($code, ClassMethodContext::create(PrivateModifier::create())),
            'code' => $code,
        ];
    }

    /** @dataProvider modifyMethodToNonClassContextProvider */
    public function testModifyMethodToNonClassContext(Method $expected, Method $method): void
    {
        self::assertEquals($expected, $this->methodModifierService->modifyMethodToNonClassContext($method));
    }

    public function modifyMethodToNonClassContextProvider(): array
    {
        $code = 'function foo(): void { // doSomething }';
        return [
            'free method' => [
                'expected' => Method::create($code, FreeMethodContext::create()),
                'method' => Method::create($code, FreeMethodContext::create()),
            ],
            'class method' => [
                'expected' => Method::create($code, FreeMethodContext::create()),
                'method' => Method::create('public ' . $code, ClassMethodContext::create(PublicModifier::create())),
            ],
        ];
    }

    public function testModifyMethodToNonClassMethodThrows(): void
    {
        $code = 'public function foo(): void { $this->log("abc"); }';
        $method = Method::create($code, ClassMethodContext::create(PublicModifier::create()));

        self::expectException(MethodCannotBeModifiedToNonClassContext::class);

        $this->methodModifierService->modifyMethodToNonClassContext($method);
    }
}