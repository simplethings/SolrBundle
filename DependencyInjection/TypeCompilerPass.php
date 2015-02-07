<?php

/**
 * (c) SimpleThings GmbH <info@simplethings.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SimpleThings\Bundle\SolrBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Simon Mönch <moench@simplethings.de>
 */
class TypeCompilerPass implements CompilerPassInterface
{
    private static $typeRegistryService = 'simplethings.solr.type_registry';
    private static $typeServiceTag = 'simplethings.solr.type';

    public function process(ContainerBuilder $container)
    {
        if (! $container->hasDefinition(self::$typeRegistryService)) {
            return;
        }

        $definition     = $container->getDefinition(self::$typeRegistryService);
        $taggedServices = $container->findTaggedServiceIds(self::$typeServiceTag);

        foreach ($taggedServices as $id => $tagAttributes) {
            foreach ($tagAttributes as $attributes) {
                $definition->addMethodCall(
                    'addType',
                    [new Reference($id), $attributes["alias"]]
                );
            }
        }
    }
}
