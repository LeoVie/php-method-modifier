<?php

declare(strict_types=1);

namespace LeoVie\PhpMethodModifier\Tests\Unit\Extractor;

use ArrayIterator;
use Generator;
use LeoVie\PhpMethodModifier\Extractor\AccessModifierExtractor;
use LeoVie\PhpMethodModifier\Model\AccessModifier\AccessModifier;
use LeoVie\PhpMethodModifier\Model\AccessModifier\AccessModifierCollection;
use LeoVie\PhpMethodModifier\Model\AccessModifier\PrivateModifier;
use LeoVie\PhpMethodModifier\Model\AccessModifier\ProtectedModifier;
use LeoVie\PhpMethodModifier\Model\AccessModifier\PublicModifier;
use PHPUnit\Framework\TestCase;

class AccessModifierExtractorTest extends TestCase
{
    private AccessModifierExtractor $accessModifierExtractor;

    protected function setUp(): void
    {
        $accessModifierCollection = new AccessModifierCollection(new ArrayIterator([
            PublicModifier::create(),
            ProtectedModifier::create(),
            PrivateModifier::create(),
        ]));

        $this->accessModifierExtractor = new AccessModifierExtractor($accessModifierCollection);
    }

    /** @dataProvider extractProvider */
    public function testExtract(?AccessModifier $expected, string $code): void
    {
        self::assertEquals($expected, $this->accessModifierExtractor->extract($code));
    }

    public function extractProvider(): Generator
    {
        yield 'empty' => [
            'expected' => null,
            'code' => '',
        ];

        $code = 'function foo(): void { // doSomething }';
        yield 'free method' => [
            'expected' => null,
            'code' => $code,
        ];

        $mapping = [
            'public' => PublicModifier::create(),
            'PUBLIC' => PublicModifier::create(),
            'pUbLIc' => PublicModifier::create(),
            'protected' => ProtectedModifier::create(),
            'PROTECTED' => ProtectedModifier::create(),
            'pRoteCTEd' => ProtectedModifier::create(),
            'private' => PrivateModifier::create(),
            'PRIVATE' => PrivateModifier::create(),
            'PriVatE' => PrivateModifier::create(),
        ];

        foreach ($mapping as $codePrefix => $accessModifier) {
            yield $codePrefix =>  [
                'expected' => $accessModifier,
                'code' => $codePrefix . ' ' . $code,
            ];
        }
    }
}