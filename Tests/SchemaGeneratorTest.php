<?php

/**
 * (c) SimpleThings GmbH <info@simplethings.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SimpleThings\Bundle\SolrBundle\Tests;

use SimpleThings\Bundle\SolrBundle\Search\SchemaGenerator;

class SchemaGeneratorTest extends TestCase
{
    public function testGenerate()
    {
        $generatedDom = new \DOMDocument();
        $generatedDom->loadXML((new SchemaGenerator($this->typeRegistry, $this->metadata))->create());

        $dom = new \DOMDocument();
        $dom->load(__DIR__ . '/Fixtures/schema/schema.xml');

        $this->assertEquals($dom, $generatedDom);
    }
} 
