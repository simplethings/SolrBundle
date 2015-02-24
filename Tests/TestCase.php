<?php

/**
 * (c) SimpleThings GmbH <info@simplethings.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SimpleThings\Bundle\SolrBundle\Tests;

use Metadata\Driver\DriverChain;
use Metadata\Driver\FileLocator;
use Metadata\MetadataFactory;
use SimpleThings\Bundle\SolrBundle\Metadata\Driver\YamlDriver;
use SimpleThings\Bundle\SolrBundle\Search\DocumentPersister;
use SimpleThings\Bundle\SolrBundle\Search\Type;
use SimpleThings\Bundle\SolrBundle\Tests\Fixtures;

abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    /** @var Fixtures\DataObject */
    protected $dataObject;

    /** @var DocumentPersister */
    protected $documentPersister;

    /** @var MetadataFactory */
    protected $metadata;

    /** @var Type\TypeRegistry */
    protected $typeRegistry;

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
                new FileLocator(['SimpleThings\Bundle\SolrBundle\Tests\Fixtures' => __DIR__ . '/Fixtures/yml'])
            )
        ]);
        $this->metadata = new MetadataFactory($driverChain);

        $this->typeRegistry = new Type\TypeRegistry();
        $this->typeRegistry
            ->addType(new Type\StringType(), 'string')
            ->addType(new Type\CollectionType(), 'collection')
            ->addType(new Type\EmbeddedType(), 'embedded')
            ->addType(new Type\TextSpellType(), 'textSpell');

        $this->documentPersister = new DocumentPersister($this->metadata, $this->typeRegistry);
    }
}