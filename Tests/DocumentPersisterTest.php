<?php

/**
 * (c) SimpleThings GmbH <info@simplethings.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SimpleThings\Bundle\SolrBundle\Tests;

class DocumentPersisterTest extends TestCase
{
    public function testCreate()
    {
        $document = $this->documentPersister->prepare($this->dataObject);
        $fields   = $document->getFields();

        $this->assertArrayNotHasKey('fulltext', $fields);
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
}
