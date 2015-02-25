<?php

/**
 * (c) SimpleThings GmbH <info@simplethings.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SimpleThings\Bundle\SolrBundle\Metadata;

use Metadata\PropertyMetadata as BasePropertyMetadata;

/**
 * @author Simon Mönch <moench@simplethings.de>
 */
class PropertyMetadata extends BasePropertyMetadata
{
    const EMBED_ONE            = 'embedded';
    const EMBED_MANY           = 'collection';
    const EMBED_SEPARATOR      = '_embedded_';
    const COLLECTION_SEPARATOR = '_collection_';

    /** @var string */
    public $type;

    /** @var string */
    public $path;

    /** @var bool */
    public $mapped;

    /** @var array */
    public $params;

    /**
     * @param $key
     *
     * @return null|string
     */
    public function get($key)
    {
        if (isset($this->params[$key])) {
            return $this->params[$key];
        }

        return null;
    }
} 
