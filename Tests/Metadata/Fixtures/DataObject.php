<?php

namespace SimpleThings\Bundle\SolrBundle\Tests\Metadata\Fixtures;

class DataObject extends ParentClass
{
    /** @var string */
    public $name;

    /** @var int */
    public $integer;

    /** @var TestObject */
    public $embedded;

    /** @var TestObject[] */
    public $collection = array();

    public function __construct($name)
    {
        $this->name = $name;
    }
}
