<?php

declare(strict_types=1);

namespace LeoVie\PhpMethodModifier\Tests\Functional;

use LeoVie\PhpMethodModifier\Service\MethodModifierService;
use PHPUnit\Framework\TestCase;

class FrameworkTest extends TestCase
{
    public function testServiceWiring(): void
    {
        $kernel = new TestingKernel('test', true);
        $kernel->boot();
        $grouper = $kernel->getContainer()->get(MethodModifierService::class);

        self::assertInstanceOf(MethodModifierService::class, $grouper);
    }
}