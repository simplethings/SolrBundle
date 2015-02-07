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
class StringType extends Type
{
    /**
     * @param string $value
     *
     * @return string|string
     */
    public function convertValue($value)
    {
        return strip_tags((string) $value);
    }
}
