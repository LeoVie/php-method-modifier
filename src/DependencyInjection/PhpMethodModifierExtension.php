<?php

declare(strict_types=1);

namespace LeoVie\PhpMethodModifier\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class PhpMethodModifierExtension extends Extension
{
    /**
     * @param mixed[] $configs
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configDir = new FileLocator(__DIR__ . '/../../config/');

        $loader = new YamlFileLoader($container, $configDir);
        $loader->load('services.yaml');
    }
}