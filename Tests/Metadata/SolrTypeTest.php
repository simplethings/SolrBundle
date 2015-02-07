<?php

/**
 * (c) SimpleThings GmbH <info@simplethings.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SimpleThings\Bundle\SolrBundle\Tests\Metadata;

use Metadata\Driver\DriverChain;
use Metadata\Driver\FileLocator;
use Metadata\MetadataFactory;
use SimpleThings\Bundle\SolrBundle\Metadata\Driver\YamlDriver;
use SimpleThings\Bundle\SolrBundle\Search\Persister\DocumentPersister;
use SimpleThings\Bundle\SolrBundle\Search\SchemaGenerator;
use SimpleThings\Bundle\SolrBundle\Search\Type;

class SolrTypeTest extends \PHPUnit_Framework_TestCase
{
    /** @var Fixtures\DataObject */
    private $dataObject;

    /** @var DocumentPersister */
    private $documentPersister;

    /** @var MetadataFactory */
    private $metadata;

    /** @var Type\TypeRegistry */
    private $typeRegistry;

    public function setUp()
    {
        $testObject1           = new Fixtures\TestObject('collection1');
        $testObject1->embedded = new Fixtures\TestObject2('test1');

        $testObject2           = new Fixtures\TestObject('collection2');
        $testObject2->embedded = new Fixtures\TestObject2('test2');

        $this->dataObject             = new Fixtures\DataObject('test');
        $this->dataObject->collection = [$testObject1, $testObject2];

        $driverChain    = new DriverChain([
            new YamlDriver(
                new FileLocator(['SimpleThings\Bundle\SolrBundle\Tests\Metadata\Fixtures' => __DIR__ . '/Fixtures/yml'])
            )
        ]);
        $this->metadata = new MetadataFactory($driverChain);

        $this->typeRegistry = new Type\TypeRegistry();
        $this->typeRegistry
            ->addType(new Type\StringType(), 'string')
            ->addType(new Type\CollectionType(), 'collection')
            ->addType(new Type\EmbeddedType(), 'embedded');

        $this->documentPersister = new DocumentPersister($this->metadata, $this->typeRegistry);
    }

    public function testCreate()
    {
        $document = $this->documentPersister->prepare($this->dataObject);
        $fields   = $document->getFields();

        $this->assertEquals($this->dataObject->name, $fields['name']);
        $this->assertEquals(
            array_map(
                function ($object) {
                    return $object->name;
                },
                $this->dataObject->collection
            ),
            $fields['collection_collection_name']
        );
        $this->assertEquals(
            array_map(
                function ($object) {
                    return $object->embedded->name;
                },
                $this->dataObject->collection
            ),
            $fields['collection_collection_embedded_embedded_name']
        );
    }

    public function testGenerate()
    {
        $generatedDom = new \DOMDocument();
        $generatedDom->loadXML((new SchemaGenerator($this->typeRegistry, $this->metadata))->create());

        $dom = new \DOMDocument();
        $dom->load(__DIR__ . '/Fixtures/schema/schema.xml');

        $this->assertEquals($dom, $generatedDom);
    }
} 
