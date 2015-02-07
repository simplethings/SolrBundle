<?php

/**
 * (c) SimpleThings GmbH <info@simplethings.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SimpleThings\Bundle\SolrBundle\Metadata;

use Metadata\MergeableClassMetadata;
use Metadata\MergeableInterface;

/**
 * @author Simon Mönch <moench@simplethings.de>
 */
class ClassMetadata extends MergeableClassMetadata
{
    public $type;

    public function merge(MergeableInterface $object)
    {
        if (! $object instanceof ClassMetadata) {
            throw new \InvalidArgumentException('$object must be an instance of ClassMetadata.');
        }

        $this->name             = $object->name;
        $this->type             = $object->type;
        $this->reflection       = $object->reflection;
        $this->methodMetadata   = array_merge($this->methodMetadata, $object->methodMetadata);
        $this->propertyMetadata = array_merge($this->propertyMetadata, $object->propertyMetadata);
        $this->fileResources    = array_merge($this->fileResources, $object->fileResources);

        if ($object->createdAt < $this->createdAt) {
            $this->createdAt = $object->createdAt;
        }
    }

    public function serialize()
    {
        return serialize(
            [
                $this->name,
                $this->type,
                $this->methodMetadata,
                $this->propertyMetadata,
                $this->fileResources,
                $this->createdAt,
            ]
        );
    }

    public function unserialize($str)
    {
        list(
            $this->name,
            $this->type,
            $this->methodMetadata,
            $this->propertyMetadata,
            $this->fileResources,
            $this->createdAt
            ) = unserialize($str);

        $this->reflection = new \ReflectionClass($this->name);
    }
}
