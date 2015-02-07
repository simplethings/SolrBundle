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
class HTMLType extends StringType
{
    /**
     * @param string $value
     *
     * @return string
     */
    public function convertValue($value)
    {
        return $value;
    }
}
