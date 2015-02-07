<?php

/**
 * (c) SimpleThings GmbH <info@simplethings.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SimpleThings\Bundle\SolrBundle\Search\Type;

use SimpleThings\Bundle\SolrBundle\Metadata\PropertyMetadata;
use SimpleThings\Bundle\SolrBundle\Search\Field\SchemaField;

/**
 * @author Simon Mönch <moench@simplethings.de>
 */
abstract class Type
{
    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function convertValue($value)
    {
        return $value;
    }

    /**
     * @param PropertyMetadata $metadata
     * @param SchemaField      $schema
     */
    public function prepareForSchema(PropertyMetadata $metadata, SchemaField $schema)
    {
        $schema->setFullText($metadata->fullText);
        $schema->setType('string');
    }
}
