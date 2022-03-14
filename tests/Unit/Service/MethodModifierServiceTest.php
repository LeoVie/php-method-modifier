<?php

namespace LeoVie\PhpMethodModifier\Tests\Unit\Service;

use LeoVie\PhpMethodModifier\Exception\MethodCannotBeModifiedToNonClassContext;
use LeoVie\PhpMethodModifier\Extractor\AccessModifierExtractor;
use LeoVie\PhpMethodModifier\Model\AccessModifier\AccessModifier;
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
    /** @dataProvider buildMethodProvider */
    public function testBuildMethod(Method $expected, string $code, ?AccessModifier $accessModifier): void
    {
        $accessModifierExtractor = $this->createMock(AccessModifierExtractor::class);
        $accessModifierExtractor->method('extract')->willReturn($accessModifier);
        $methodModifierService = new MethodModifierService($accessModifierExtractor);

        self::assertEquals($expected, $methodModifierService->buildMethod($code));
    }

    public function buildMethodProvider(): \Generator
    {
        $code = 'function foo(): void { // doSomething } ';
        yield 'free method' => [
            'expected' => Method::create(trim($code), FreeMethodContext::create()),
            'code' => $code,
            'accessModifier' => null,
        ];

        $code = 'public function foo(): void { // doSomething }';
        yield 'public class method' => [
            'expected' => Method::create($code, ClassMethodContext::create(PublicModifier::create())),
            'code' => ' ' . $code,
            'accessModifier' => PublicModifier::create(),
        ];

        $code = 'protected function foo(): void { // doSomething }';
        yield 'protected class method' => [
            'expected' => Method::create($code, ClassMethodContext::create(ProtectedModifier::create())),
            'code' => $code,
            'accessModifier' => ProtectedModifier::create(),
        ];

        $code = 'private function foo(): void { // doSomething }';
        yield 'private class method' => [
            'expected' => Method::create($code, ClassMethodContext::create(PrivateModifier::create())),
            'code' => $code,
            'accessModifier' => PrivateModifier::create(),
        ];
    }

    /** @dataProvider modifyMethodToNonClassMethodProvider */
    public function testModifyMethodToNonClassMethod(Method $expected, Method $method): void
    {
        $accessModifierExtractor = $this->createMock(AccessModifierExtractor::class);
        $methodModifierService = new MethodModifierService($accessModifierExtractor);

        self::assertEquals($expected, $methodModifierService->modifyMethodToNonClassContext($method));
    }

    public function modifyMethodToNonClassMethodProvider(): array
    {
        $code = 'function foo(): void { }';

        return [
            'free method' => [
                'expected' => Method::create($code, FreeMethodContext::create()),
                'method' => Method::create($code, FreeMethodContext::create()),
            ],
            'class method' => [
                'expected' => Method::create($code, FreeMethodContext::create()),
                'method' => Method::create('public ' . $code, ClassMethodContext::create(PublicModifier::create())),
            ],
            'static class method' => [
                'expected' => Method::create($code, FreeMethodContext::create()),
                'method' => Method::create('private static ' . $code, ClassMethodContext::create(PrivateModifier::create())),
            ],
        ];
    }

    public function testModifyMethodToNonClassMethodThrows(): void
    {
        $code = 'public function foo(): void { $this->log("abc"); }';
        $method = Method::create($code, ClassMethodContext::create(PublicModifier::create()));

        self::expectException(MethodCannotBeModifiedToNonClassContext::class);

        $accessModifierExtractor = $this->createMock(AccessModifierExtractor::class);
        $methodModifierService = new MethodModifierService($accessModifierExtractor);
        $methodModifierService->modifyMethodToNonClassContext($method);
    }
}