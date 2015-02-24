<?php

/**
 * (c) SimpleThings GmbH <info@simplethings.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SimpleThings\Bundle\SolrBundle\Metadata\Driver;

use Metadata\Driver\AbstractFileDriver;
use SimpleThings\Bundle\SolrBundle\Metadata\ClassMetadata;
use SimpleThings\Bundle\SolrBundle\Metadata\PropertyMetadata;
use Symfony\Component\Yaml\Yaml;

/**
 * @author Simon Mönch <moench@simplethings.de>
 */
class YamlDriver extends AbstractFileDriver
{
    /** @var array */
    private $fields = [];

    /** @var string */
    private $class;

    /** @var string */
    private $type;

    /**
     * Parses the content of the file, and converts it to the desired metadata.
     *
     * @param \ReflectionClass $class
     * @param string           $file
     *
     * @throws \RuntimeException
     * @return \MetaData\ClassMetadata|null
     */
    protected function loadMetadataFromFile(\ReflectionClass $class, $file)
    {
        $config = Yaml::parse(file_get_contents($file));

        if (! isset($config[$name = $class->name])) {
            throw new \RuntimeException(sprintf(
                'Expected metadata for class %s to be defined in %s.',
                $class->name,
                $file
            ));
        }

        $config   = $config[$name];
        $metadata = new ClassMetadata($name);

        $metadata->fileResources[] = $file;
        $metadata->fileResources[] = $class->getFileName();

        $metadata->type = $config['type'];

        /** @var PropertyMetadata[] $propertiesMetadata */
        $propertiesMetadata = [];

        foreach ($class->getProperties() as $property) {
            if ($name !== $property->class) {
                continue;
            }

            $pName = $property->getName();

            $propertiesMetadata[$pName] = new PropertyMetadata($name, $pName);
        }

        foreach ($propertiesMetadata as $pName => $pMetadata) {
            if (! isset($config['fields'][$pName])) {
                continue;
            }

            $pConfig = $config['fields'][$pName];

            if (is_array($pConfig)) {
                if (isset($pConfig['type'])) {
                    $pMetadata->type = $pConfig['type'];
                    unset($pConfig['type']);
                }

                if (isset($pConfig['path'])) {
                    $pMetadata->path = $pConfig['path'];
                    unset($pConfig['path']);
                }

                if (isset($pConfig['mapped'])) {
                    $pMetadata->mapped = $pConfig['mapped'];
                    unset($pConfig['mapped']);
                }

                $pMetadata->params = $pConfig;
            } else {
                $pMetadata->type = $pConfig;
            }

            $metadata->addPropertyMetadata($pMetadata);
        }

        return $metadata;
    }

    /**
     * Returns the extension of the file.
     *
     * @return string
     */
    protected function getExtension()
    {
        return 'yml';
    }
}
