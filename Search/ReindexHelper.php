<?php

/**
 * (c) SimpleThings GmbH <info@simplethings.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SimpleThings\Bundle\SolrBundle\Search;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Console\Helper\ProgressHelper;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Simon Mönch <moench@simplethings.de>
 * @author David Badura <badura@simplethings.de>
 */
class ReindexHelper
{
    /**
     * @var SearchManager
     */
    private $manager;

    /**
     * @var EntityRepository
     */
    private $repository;

    public function __construct(SearchManager $manager, EntityRepository $repository)
    {
        $this->manager    = $manager;
        $this->repository = $repository;
    }

    /**
     * @param OutputInterface $output
     */
    public function doReindex(OutputInterface $output)
    {
        $progress = new ProgressHelper();

        $sm = $this->manager;
        $sm->truncateIndex();

        $entities = $this->repository->findAll();
        $progress->start($output, count($entities));

        $index = 0;
        foreach ($entities as $entity) {
            $sm->persist($entity);

            if (($index % 500) === 0) {
                $sm->flush();
            }

            $progress->advance();
            $index++;
        }

        $sm->flush();
        $progress->finish();
    }
}
