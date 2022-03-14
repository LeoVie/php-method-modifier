<?php

declare(strict_types=1);

namespace LeoVie\PhpMethodModifier\Tests\Functional;

use LeoVie\PhpMethodModifier\PhpMethodModifierBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class TestingKernel extends Kernel
{
    public function registerBundles(): array
    {
        return [
            new PhpMethodModifierBundle()
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
    }
}