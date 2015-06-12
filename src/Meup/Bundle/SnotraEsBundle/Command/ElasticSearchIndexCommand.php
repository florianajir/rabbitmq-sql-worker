<?php

namespace Meup\Bundle\SnotraEsBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Elastica\Index;

/**
 *
 */
class ElasticSearchIndexCommand extends ContainerAwareCommand
{
    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('elasticsearch:index')
            ->setDescription('')
            ->addArgument('index', InputArgument::OPTIONAL, '')
            ->addArgument('action', InputArgument::OPTIONAL, '', 'show')
        ;
    }

    /**
     * @param Index $index
     * @param OutputInterface $output
     * @param boolean $force
     * 
     * @return void
     */
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

    /**
     * @param Index $index
     * @param InputInterface $input
     * @param OutputInterface $output
     * 
     * @return void
     */
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

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $indices = $this
            ->getContainer()
            ->get('meup_snotra.elastica_index.dictionary')
        ;
        $index_name = $input->getArgument('index');
        $action     = $input->getArgument('action');
        $index      = $indices->offsetExists($index_name) 
                    ? $indices
                        ->offsetGet($index_name)
                    : $this
                        ->getContainer()
                        ->get('meup_snotra.elastica_client')
                        ->getIndex($index_name)
        ;

        switch ($action) {
            case 'create':
                if ($index) {
                    $this->create($index, $output, true);
                } else {
                    // do nothing, throw an exception
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

    /**
     * {@inheritDoc}
     */
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
