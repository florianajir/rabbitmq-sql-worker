<?php

namespace Meup\Bundle\SnotraBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Elastica\Index;

class ElasticSearchIndexCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('elasticsearch:index')
            ->setDescription('')
            ->addArgument('action', InputArgument::OPTIONAL, '', 'show')
            ->addArgument('index', InputArgument::OPTIONAL, '')
            //->addOption('force', null, InputOption::VALUE_NONE, '')
            //->addOption('yell', null, InputOption::VALUE_NONE, 'Si définie, la tâche criera en majuscules')
        ;
    }

    private function create(Index $index, OutputInterface $output, $force = false)
    {
        if (!$index->exists() || $force) {
            $index
                ->create(
                    $this
                        ->getContainer()
                        ->getParameter(
                            sprintf(
                                'elasticsearch_%s_index',
                                $index->getName()
                            )
                        ),
                    true
                )
            ;
        }
    }

    private function show(Index $index, InputInterface $input, OutputInterface $output)
    {
        foreach ($index->getStats()->getData()['indices'][$index->getName()] as $name => $stats) {
            $output->writeln('<fg=yellow>'.$index->getName().' : '.$name.'</fg=yellow>');

            foreach ($stats as $section => $values) {
                $output->writeln('');
                $output->writeln('  <fg=green>'.$section.'</fg=green>');
                $output->writeln(str_repeat('-', 15*count($values)+1));
                foreach ($values as $key => $value) {
                    $output->write(sprintf("| %12.12s ", $key));
                }
                $output->writeln('|');
                $output->writeln(str_repeat('-', 15*count($values)+1));
                foreach ($values as $key => $value) {
                    $output->write(sprintf("| %12.12s ", $value));
                }
                $output->writeln('|');
                $output->writeln(str_repeat('-', 15*count($values)+1));
            }
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $indices = $this
            ->getContainer()
            ->get('meup_snotra.elastica_index.dictionary')
        ;
        $index_name = $input->getArgument('index');
        $action     = $input->getArgument('action');

        if ($indices->offsetExists($index_name)) {
            $index = $indices->offsetGet($index_name);
        }

        switch ($action) {
            case 'create':
                if ($index) {
                    $this->create($index, $output, true);
                } else {
                    // specify an index
                }
                break;
            case 'show':
            default:
                if ($index) {
                    $this->show($index, $input, $output);
                } else {
                    foreach ($indices as $index) {
                        $this->show($index, $input, $output);
                    }
                }
                break;
        }
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $action = $input->getArgument('action');

        if ($action!='show' && !$input->getArgument('index')) {
            $input->setArgument(
                'index',
                $this
                    ->getHelper('index')
                    ->askAndValidate()
            );
        }
    }
}
