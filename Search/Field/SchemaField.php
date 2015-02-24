<?php

/**
 * (c) SimpleThings GmbH <info@simplethings.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SimpleThings\Bundle\SolrBundle\Search\Field;

/**
 * @author Simon Mönch <moench@simplethings.de>
 */
class SchemaField
{
    /** @var string */
    private $name;

    /** @var bool */
    private $indexed = true;

    /** @var bool */
    private $stored = true;

    /** @var array */
    private $type = 'string';

    /** @var bool */
    private $multiValued = false;

    /** @var bool */
    private $required = false;

    /** @var array */
    private $copy = array();

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return array(
            'name'        => $this->name,
            'type'        => $this->type,
            'indexed'     => $this->indexed,
            'stored'      => $this->stored,
            'multiValued' => $this->multiValued,
            'required'    => $this->required
        );
    }

    /**
     * @param boolean $indexed
     */
    public function setIndexed($indexed)
    {
        $this->indexed = $indexed;
    }

    /**
     * @return boolean
     */
    public function getIndexed()
    {
        return $this->indexed;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param boolean $required
     */
    public function setRequired($required)
    {
        $this->required = $required;
    }

    /**
     * @param boolean $stored
     */
    public function setStored($stored)
    {
        $this->stored = $stored;
    }

    /**
     * @param array $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @param boolean $multiValued
     */
    public function setMultiValued($multiValued)
    {
        $this->multiValued = $multiValued;
    }

    /**
     * @return array
     */
    public function getCopy()
    {
        return $this->copy;
    }

    /**
     * @param array $copy
     */
    public function setCopy(array $copy)
    {
        $this->copy = $copy;
    }
}
