<?php

namespace Meup\Bundle\SnotraBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Elastica\Type;

class ElasticSearchTypeCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('elasticsearch:type')
            ->setDescription('')
            ->addArgument('action', InputArgument::OPTIONAL, '', 'show')
            //->addOption('yell', null, InputOption::VALUE_NONE, 'Si définie, la tâche criera en majuscules')
        ;
    }

    private function show(Type $type, InputInterface $input, OutputInterface $output)
    {
    }

    private function create(Type $type, InputInterface $input, OutputInterface $output)
    {
        // Define mapping
        $mapping = new \Elastica\Type\Mapping();
        $mapping->setType($type);
        $mapping->setParam('index_analyzer', 'indexAnalyzer');
        $mapping->setParam('search_analyzer', 'searchAnalyzer');

        // Define boost field
        $mapping->setParam('_boost', array('name' => '_boost', 'null_value' => 1.0));

        // Set mapping
        $mapping->setProperties(array(
            'id'      => array('type' => 'integer', 'include_in_all' => FALSE),
            'user'    => array(
                'type' => 'object',
                'properties' => array(
                    'name'      => array('type' => 'string', 'include_in_all' => TRUE),
                    'fullName'  => array('type' => 'string', 'include_in_all' => TRUE)
                ),
            ),
            'msg'     => array('type' => 'string', 'include_in_all' => TRUE),
            'tstamp'  => array('type' => 'date', 'include_in_all' => FALSE),
            'location'=> array('type' => 'geo_point', 'include_in_all' => FALSE),
            '_boost'  => array('type' => 'float', 'include_in_all' => FALSE)
        ));

        // Send mapping to type
        $mapping->send();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $type = $this->getContainer()->get('elasticsearch.kotetou.lifeboat');

        switch ($input->getArgument('action')) {
            case 'create':
                $this->create($type, $input, $output);
                break;
            case 'show':
                $this->show($type, $input, $output);
                break;
            default:
                $action = $input->getArgument('action');
                $output->writeln("<error>unknow action argument {$action}</error>");
                break;
        }
    }
}
