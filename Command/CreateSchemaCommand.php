<?php

/**
 * (c) SimpleThings GmbH <info@simplethings.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SimpleThings\Bundle\SolrBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Simon Mönch <moench@simplethings.de>
 * @author David Badura <badura@simplethings.de>
 */
class CreateSchemaCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('simplethings:solr:create-schema')
            ->setDescription('Create Solr schema.xml')
            ->addArgument('target', InputArgument::REQUIRED, 'Target');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $target = $input->getArgument('target');

        $xml = $this->getContainer()->get('simplethings.solr.schema_generator')->create();

        file_put_contents($target, $xml);

        $output->writeln('<info>ok</info>');
    }
}
