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
class FloatType extends Type
{
    public function convertValue($value)
    {
        return (float)$value;
    }

    public function prepareForSchema(PropertyMetadata $metadata, SchemaField $schema)
    {
        parent::prepareForSchema($metadata, $schema);

        $schema->setType('float');
    }
}
