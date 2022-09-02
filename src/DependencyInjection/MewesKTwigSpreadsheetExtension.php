<?php

namespace Recranet\TwigSpreadsheetBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

/**
 * Class RecranetTwigSpreadsheetExtension.
 */
class RecranetTwigSpreadsheetExtension extends ConfigurableExtension
{
    /**
     * {@inheritdoc}
     *
     * @throws \Exception
     */
    protected function loadInternal(array $mergedConfig, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $definition = $container->getDefinition('recranet_twig_spreadsheet.twig_spreadsheet_extension');
        $definition->replaceArgument(0, $mergedConfig);
    }
}
