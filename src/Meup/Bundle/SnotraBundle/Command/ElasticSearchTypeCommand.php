<?php

namespace Meup\Bundle\SnotraBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Elastica\Type\Mapping;
use Elastica\Type;

class ElasticSearchTypeCommand extends ContainerAwareCommand
{

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('elasticsearch:type')
            ->setDescription('')
            ->addArgument('index', InputArgument::REQUIRED, '')
            ->addArgument('type', InputArgument::REQUIRED, '')
            ->addArgument('action', InputArgument::OPTIONAL, '', 'create')
        ;
    }

    /**
     * @param Type $type
     * @param bool $force
     */
    private function create(Type $type, $force = true)
    {
        if (!$type->exists() || $force) {
            $types = $this
                ->getContainer()
                ->getParameter(
                    sprintf(
                        'elasticsearch_%s_types',
                        $type->getIndex()->getName()
                    )
                )
            ;
            $config = $types['default'];

            // Define mapping
            $mapping = new Mapping();
            foreach ($config['params'] as $name => $value) {
                $mapping->setParam($name, $value);
            }
            $mapping
                ->setType($type)
                ->setProperties($config['properties'])
                ->send()
            ;
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $type = $this
            ->getContainer()
            ->get('meup_snotra.elastica_client')
            ->getIndex($input->getArgument('index'))
            ->getType($input->getArgument('type'))
        ;

        switch ($input->getArgument('action')) {
            case 'create':
                $this->create($type);
                break;
            default:
                $action = $input->getArgument('action');
                $output->writeln("<error>unknow action argument {$action}</error>");
                break;
        }
    }
}
