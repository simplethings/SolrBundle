<?php

/**
 * (c) SimpleThings GmbH <info@simplethings.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SimpleThings\Bundle\SolrBundle\Search;

use Metadata\MetadataFactory;
use Solarium\Client;
use Solarium\Core\Query\QueryInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

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
     * @var MetadataFactory
     */
    private $metadataFactory;

    /** @var \Symfony\Component\PropertyAccess\PropertyAccessor */
    private $accessor;

    /**
     *
     * @param \Solarium\Client  $client
     * @param DocumentPersister $persister
     * @param MetadataFactory   $metadataFactory
     */
    public function __construct(Client $client, DocumentPersister $persister, MetadataFactory $metadataFactory)
    {
        $this->client          = $client;
        $this->persister       = $persister;
        $this->metadataFactory = $metadataFactory;
        $this->accessor        = PropertyAccess::createPropertyAccessor();
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
        $this->persistDocuments[$this->getId($object)] = $this->persister->prepare($object);

        return $this;
    }

    /**
     * @param mixed $object
     *
     * @return $this
     */
    public function update($object)
    {
        $this->updateDocuments[$this->getId($object)] = $this->persister->prepare($object);

        return $this;
    }

    /**
     * @param mixed $object
     *
     * @return $this
     */
    public function remove($object)
    {
        $id                         = $this->getId($object);
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

    private function getId($object)
    {
        $id       = null;
        $class    = (new \ReflectionClass($object))->getName();
        $metadata = $this->metadataFactory->getMetadataForClass($class);

        foreach ($metadata->propertyMetadata as $propertyMetadata) {
            if ($propertyMetadata->type == 'id') {
                $id = $propertyMetadata->class . '_' . $this->accessor->getValue($object, $propertyMetadata->name);
                break;
            }
        }

        if (null === $id) {
            throw new \RuntimeException(sprintf('class "%s" has no field declared as "id"', $class));
        }

        return $id;
    }
} 
