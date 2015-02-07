<?php

/**
 * (c) SimpleThings GmbH <info@simplethings.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SimpleThings\Bundle\SolrBundle\Search\Type;

/**
 * @author Simon Mönch <moench@simplethings.de>
 */
class TypeRegistry
{
    /**
     * @var array
     */
    private $types;

    public function __construct()
    {
        $this->types = array();
    }

    /**
     * @param Type $type
     * @param      $alias
     *
     * @return $this
     */
    public function addType(Type $type, $alias)
    {
        $this->types[$alias] = $type;

        return $this;
    }

    /**
     * @param $alias
     *
     * @throws \Exception
     * @return Type
     */
    public function getType($alias)
    {
        if (array_key_exists($alias, $this->types)) {
            return $this->types[$alias];
        }

        throw new \Exception(sprintf('Element type "%s" wird nicht unterstützt.', $alias));
    }
}
