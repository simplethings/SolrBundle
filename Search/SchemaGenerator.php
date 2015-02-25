<?php

/**
 * (c) SimpleThings GmbH <info@simplethings.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SimpleThings\Bundle\SolrBundle\Search;

use Metadata\MetadataFactory;
use SimpleThings\Bundle\SolrBundle\Metadata\ClassMetadata;
use SimpleThings\Bundle\SolrBundle\Metadata\PropertyMetadata;
use SimpleThings\Bundle\SolrBundle\Search\Field\SchemaField;
use SimpleThings\Bundle\SolrBundle\Search\Type\TypeRegistry;

/**
 * @author David Badura <badura@simplethings.de>
 * @author Simon Mönch <moench@simplethings.de>
 */
class SchemaGenerator
{
    /** @var TypeRegistry */
    private $typeRegistry;

    /** @var MetadataFactory */
    private $metadata;

    /** @var array|SchemaField[] */
    private $fields = array();

    /** @var array|SchemaField[] */
    private $copyFields = array();

    /**
     * @param Type\TypeRegistry $typeRegistry
     * @param MetadataFactory   $metadata
     */
    public function __construct(TypeRegistry $typeRegistry, MetadataFactory $metadata)
    {
        $this->typeRegistry = $typeRegistry;
        $this->metadata     = $metadata;
    }

    /**
     *
     * @return string
     */
    public function create()
    {
        $writer = $this->createXMLWriter();
        $writer->startElement("fields");

        foreach ($this->metadata->getAllClassNames() as $class) {
            /** @var ClassMetadata $metadata */
            $metadata = $this->metadata->getMetadataForClass($class);

            if ($metadata->type == 'document') {
                $this->createFieldsFromClass($class);
            }
        }

        $this->addSchemaFields($writer, $this->fields);
        $this->addCopyFields($writer, $this->copyFields);

        $writer->endElement();
        $writer->endDocument();

        return $writer->outputMemory();
    }

    /**
     *
     * @param \XMLWriter    $writer
     * @param SchemaField[] $fields
     */
    protected function addCopyFields(\XMLWriter $writer, array $fields)
    {
        foreach ($fields as $field) {
            $this->addElement(
                $writer,
                'copyField',
                null,
                array(
                    'source' => $field['source'],
                    'dest'   => $field['destination']
                )
            );
        }
    }

    /**
     *
     * @param \XMLWriter $writer
     * @param string     $element
     * @param string     $value
     * @param array      $attributes
     */
    protected function addElement(\XMLWriter $writer, $element, $value = null, array $attributes = [])
    {
        $writer->startElement($element);

        foreach ($attributes as $key => $attribute) {

            if (is_bool($attribute)) {
                $attribute = ($attribute) ? 'true' : 'false';
            }

            $writer->writeAttribute($key, $attribute);
        }

        if ($value !== null) {
            $writer->text($value);
        }

        $writer->endElement();
    }

    /**
     *
     * @return \XMLWriter
     */
    protected function createXMLWriter()
    {
        $writer = new \XMLWriter();
        $writer->openMemory();
        $writer->setIndent(true);
        $writer->startDocument("1.0", "UTF-8");

        return $writer;
    }

    /**
     * @param string           $class
     * @param string           $prefix
     * @param PropertyMetadata $parent
     *
     * @throws \RuntimeException
     */
    private function createFieldsFromClass($class, $prefix = null, PropertyMetadata $parent = null)
    {
        $metadata = $this->metadata->getMetadataForClass($class);

        if (! $metadata) {
            throw new \RuntimeException('No Metadata for class exists: ' . $class);
        }

        foreach ($metadata->propertyMetadata as $field) {
            $name = $prefix . $field->name;

            if (PropertyMetadata::EMBED_ONE == $field->type) {
                $this->createFieldsFromClass(
                    $field->get('class'),
                    $name . PropertyMetadata::EMBED_SEPARATOR,
                    $parent
                );
            } elseif (PropertyMetadata::EMBED_MANY == $field->type) {
                $this->createFieldsFromClass(
                    $field->get('class'),
                    $name . PropertyMetadata::COLLECTION_SEPARATOR,
                    $field
                );
            } else {
                $schemaField = new SchemaField($name);
                $this->prepareForSchema($field, $schemaField);

                if (null !== $parent) {
                    $this->prepareForSchema($parent, $schemaField);
                }

                $this->fields[$name] = $schemaField;

                foreach ($schemaField->getCopy() as $copyFieldDestination) {
                    $this->copyFields[] = array(
                        'source'      => $name,
                        'destination' => $copyFieldDestination
                    );
                }
            }
        }
    }

    /**
     * @param               $writer
     * @param SchemaField[] $fields
     */
    private function addSchemaFields($writer, $fields)
    {
        foreach ($fields as $field) {
            $this->addElement($writer, 'field', null, $field->getAttributes());
        }
    }

    /**
     * @param PropertyMetadata $field
     * @param SchemaField      $schemaField
     *
     * @return Type\Type
     */
    private function prepareForSchema(PropertyMetadata $field, SchemaField $schemaField)
    {
        $this->typeRegistry->getType($field->type)->prepareForSchema($field, $schemaField);
    }
}
