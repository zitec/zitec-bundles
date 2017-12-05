<?php

namespace Zitec\FormAutocompleteBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Loads and manages the bundle DI configuration.
 */
class ZitecFormAutocompleteExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        if (Kernel::MAJOR_VERSION > 2) {
            $loader->load('services_symfony3.0.yml');
        } else {
            $loader->load('services_symfony2.8.yml');
        }

        foreach ($config as $key => $value) {
            $container->setParameter("zitec.form_autocomplete.$key", $value);
        }
    }
}
