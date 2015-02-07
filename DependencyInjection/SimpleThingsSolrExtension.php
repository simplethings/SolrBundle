<?php

/**
 * (c) SimpleThings GmbH <info@simplethings.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SimpleThings\Bundle\SolrBundle\DependencyInjection;

use Symfony\Component\Config as Config;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * @author Simon Mönch <moench@simplethings.de>
 */
class SimpleThingsSolrExtension extends Extension
{
    /**
     * @param array            $configs
     * @param ContainerBuilder $container
     *
     * @throws \InvalidArgumentException
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);

        $files       = $this->convertConfigFiles($config['config_files']);
        $fileLocator = new Definition('Metadata\Driver\FileLocator', [$files]);
        $yamlDriver  = new Definition('SimpleThings\Bundle\SolrBundle\Metadata\Driver\YamlDriver', [$fileLocator]);
        $driverChain = new Definition('Metadata\Driver\DriverChain', [[$yamlDriver]]);

        $container->setDefinition(
            'simplethings.solr.metadata',
            new Definition('Metadata\MetadataFactory', [$driverChain])
        );

        $loader = new Loader\XmlFileLoader($container, new Config\FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');
    }

    /**
     * @param $config
     *
     * @return array
     */
    protected function convertConfigFiles($config)
    {
        $files = [];

        foreach ($config as $location) {
            $files[$location['prefix']] = $location['path'];
        }

        return $files;
    }
}
