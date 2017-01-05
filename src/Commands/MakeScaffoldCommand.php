<?php

namespace Rougin\Combustor\Commands;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Make Scaffold Command
 *
 * @package Combustor
 * @author  Rougin Royce Gutib <rougingutib@gmail.com>
 */
class MakeScaffoldCommand extends AbstractCommand
{
    /**
     * Set the configurations of the specified command.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('make:scaffold')->setDescription('Create a new controller class, model class, and view folder');
        $this->addArgument('table', InputArgument::REQUIRED, 'Name of the table');
        $this->addOption('type', null, InputArgument::OPTIONAL, 'Type of model: Either Credo or Wildfire', 'wildfire');
    }

    /**
     * Executes the current command.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @param  \Symfony\Component\Console\Input\OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $commands = [ 'make:controller', 'make:model', 'make:view' ];

        foreach ($commands as $command) {
            $arguments  = [ 'command' => $command, 'table' => $input->getArgument('table') ];

            if ($command == 'make:model') {
                $arguments['--type'] = $input->getOption('type');
            }

            $application = $this->getApplication()->find($command);

            $application->run(new ArrayInput($arguments), $output);
        }
    }
}
