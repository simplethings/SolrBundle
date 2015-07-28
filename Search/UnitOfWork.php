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
class UnitOfWork
{
    /**
     *
     * @var DocumentPersister
     */
    private $persister;

    /**
     *
     * @var Client
     */
    private $client;

    /**
     *
     * @var array
     */
    private $persistDocuments = array();

    /**
     *
     * @var array
     */
    private $removeDocuments = array();

    /**
     *
     * @var array
     */
    private $updateDocuments = array();

    /**
     *
     * @param \Solarium\Client  $client
     * @param DocumentPersister $persister
     */
    public function __construct(Client $client, DocumentPersister $persister)
    {
        $this->client    = $client;
        $this->persister = $persister;
    }

    /**
     * @param QueryInterface $query
     *
     * @return \Solarium\Core\Query\Result\ResultInterface
     */
    public function execute(QueryInterface $query)
    {
        return $this->client->execute($query);
    }

    /**
     * @param mixed $object
     *
     * @return $this
     */
    public function persist($object)
    {
        $this->persistDocuments[$object->getId()] = $this->persister->prepare($object);

        return $this;
    }

    /**
     * @param mixed $object
     *
     * @return $this
     */
    public function update($object)
    {
        $this->updateDocuments[$object->getId()] = $this->persister->prepare($object);

        return $this;
    }

    /**
     * @param mixed $object
     *
     * @return $this
     */
    public function remove($object)
    {
        $id                         = $object->getId();
        $this->removeDocuments[$id] = $id;

        return $this;
    }

    /**
     *
     */
    public function flush()
    {
        $update = $this->client->createUpdate();

        foreach ($this->persistDocuments as $document) {
            $update->addDocument($document);
        }

        foreach ($this->updateDocuments as $document) {
            $update->addDocument($document, true);
        }

        foreach ($this->removeDocuments as $id) {
            $update->addDeleteById($id);
        }

        $update->addCommit();
        $this->client->update($update);

        $this->persistDocuments = array();
        $this->removeDocuments  = array();
        $this->updateDocuments  = array();
    }

    /**
     *
     */
    public function truncateIndex()
    {
        $update = $this->client->createUpdate();

        $update->addDeleteQuery('*:*');
        $update->addCommit();

        $this->client->update($update);
    }

    public function createSelect()
    {
        return $this->client->createSelect();
    }
} 
