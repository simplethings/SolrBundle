<?php

namespace SimpleThings\Bundle\SolrBundle\Tests\Fixtures;

class TestObject
{
    /** @var string */
    public $name;

    /** @var TestObject2 */
    public $embedded;

    public function __construct($name)
    {
        $this->name = $name;
    }
}
