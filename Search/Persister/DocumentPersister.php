<?php

/**
 * (c) SimpleThings GmbH <info@simplethings.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SimpleThings\Bundle\SolrBundle\Search\Persister;

use Metadata\MetadataFactory;
use SimpleThings\Bundle\SolrBundle\Metadata\ClassMetadata;
use SimpleThings\Bundle\SolrBundle\Metadata\PropertyMetadata;
use SimpleThings\Bundle\SolrBundle\Search\Type\TypeRegistry;
use Solarium\QueryType\Update\Query\Document\Document;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * @author Simon Mönch <moench@simplethings.de>
 * @author David Badura <badura@simplethings.de>
 */
class DocumentPersister
{
    private $metadata;
    private $typeRegistry;
    private $accessor;

    public function __construct(MetadataFactory $metadata, TypeRegistry $typeRegistry)
    {
        $this->metadata     = $metadata;
        $this->typeRegistry = $typeRegistry;
        $this->accessor     = PropertyAccess::createPropertyAccessor();
    }

    public function prepare($object)
    {
        $class    = (new \ReflectionClass($object))->getName();
        $document = new Document();
        $this->prepareObject($document, $class, $object);

        return $document;
    }

    private function prepareObject(Document $document, $class, $object, $prefix = null)
    {
        /** @var ClassMetadata $metadata */
        $metadata = $this->metadata->getMetadataForClass($class);

        foreach ($metadata->propertyMetadata as $field) {
            $name  = $prefix . $field->name;
            $value = $this->getValue($object, $field);

            if (PropertyMetadata::EMBED_ONE == $field->type) {
                $this->prepareObject(
                    $document,
                    $field->get('class'),
                    $value,
                    $name . PropertyMetadata::EMBED_SEPARATOR
                );
            } elseif (PropertyMetadata::EMBED_MANY == $field->type) {
                $this->prepareObject(
                    $document,
                    $field->get('class'),
                    $value,
                    $name . PropertyMetadata::COLLECTION_SEPARATOR
                );
            } else {
                $this->addField($document, $value, $field, $name);
            }
        }
    }

    /**
     * @param PropertyMetadata $field
     * @param                  $value
     *
     * @return mixed
     */
    private function convertValue(PropertyMetadata $field, $value)
    {
        return $this->typeRegistry->getType($field->type)->convertValue($value);
    }

    /**
     * @param object|array     $object
     * @param PropertyMetadata $field
     *
     * @return array
     */
    private function getValue($object, PropertyMetadata $field)
    {
        $value = null;
        $path  = $field->path ? $field->path : $field->name;

        if (is_array($object) || ($object instanceof \Traversable)) {
            $value = array();
            foreach ($object as $o) {
                if (null !== $o) {
                    $value[] = $this->accessor->getValue($o, $path);
                }
            }
        } elseif (null !== $object) {
            $value = $this->accessor->getValue($object, $path);
        }

        return $value;
    }

    /**
     * @param Document $document
     * @param          $value
     * @param          $field
     * @param          $name
     */
    private function addField(Document $document, $value, $field, $name)
    {
        if (is_array($value)) {
            $values = array();
            foreach ($value as $v) {
                $values[] = $this->convertValue($field, $v);
            }
            $document->addField($name, $values);
        } else {
            $document->addField($name, $this->convertValue($field, $value));
        }
    }
} 
