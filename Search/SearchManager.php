<?php

/**
 * (c) SimpleThings GmbH <info@simplethings.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SimpleThings\Bundle\SolrBundle\Search;

use Solarium\Client;
use Solarium\Core\Query\QueryInterface;

/**
 * @author Simon Mönch <moench@simplethings.de>
 * @author David Badura <badura@simplethings.de>
 */
class SearchManager
{
    /**
     *
     * @var UnitOfWork
     */
    private $unitOfWork;

    /**
     *
     * @param \Solarium\Client  $client
     * @param DocumentPersister $persister
     */
    public function __construct(Client $client, DocumentPersister $persister)
    {
        $this->unitOfWork = new UnitOfWork($client, $persister);
    }

    /**
     * @param QueryInterface $query
     *
     * @return \Solarium\Core\Query\Result\ResultInterface
     */
    public function execute(QueryInterface $query)
    {
        return $this->unitOfWork->execute($query);
    }

    /**
     * @param mixed $object
     *
     * @return $this
     */
    public function persist($object)
    {
        $this->unitOfWork->persist($object);

        return $this;
    }

    /**
     * @param mixed $object
     *
     * @return $this
     */
    public function update($object)
    {
        $this->unitOfWork->update($object);

        return $this;
    }

    /**
     * @param mixed $object
     *
     * @return $this
     */
    public function remove($object)
    {
        $this->unitOfWork->remove($object);

        return $this;
    }

    /**
     *
     */
    public function flush()
    {
        $this->unitOfWork->flush();
    }

    /**
     *
     */
    public function truncateIndex()
    {
        $this->unitOfWork->truncateIndex();
    }

    public function createSelect()
    {
        return $this->unitOfWork->createSelect();
    }
}
