<?php

namespace Rougin\Combustor\Commands;

use Rougin\Combustor\Common\Tools;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Create Scaffold Command
 *
 * Generates a Wildfire or Doctrine-based controller,
 * model and view files for CodeIgniter
 * 
 * @package Combustor
 * @author  Rougin Royce Gutib <rougingutib@gmail.com>
 */
class CreateScaffoldCommand extends AbstractCommand
{
    /**
     * Checks whether the command is enabled or not in the current environment.
     *
     * Override this to check for x or y and return false if the command can not
     * run properly under the current conditions.
     *
     * @return bool
     */
    public function isEnabled()
    {
        return Tools::isCommandEnabled();
    }

    /**
     * Sets the configurations of the specified command.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('create:scaffold')
            ->setDescription('Creates a new controller, model and view')
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'Name of the controller, model and view'
            )->addOption(
                'bootstrap',
                NULL,
                InputOption::VALUE_NONE,
                'Includes the Bootstrap CSS/JS Framework tags'
            )->addOption(
                'camel',
                NULL,
                InputOption::VALUE_NONE,
                'Uses the camel case naming convention'
            )->addOption(
                'doctrine',
                NULL,
                InputOption::VALUE_NONE,
                'Uses the Doctrine\'s specifications'
            )->addOption(
                'keep',
                null,
                InputOption::VALUE_NONE,
                'Keeps the name to be used'
            )->addOption(
                'lowercase',
                null,
                InputOption::VALUE_NONE,
                'Keeps the first character of the name to lowercase'
            )->addOption(
                'wildfire',
                NULL,
                InputOption::VALUE_NONE,
                'Uses the Wildfire\'s specifications'
            );
    }

    /**
     * Executes the command.
     * 
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return object|\Symfony\Component\Console\Output\OutputInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $commands = [
            'create:controller',
            'create:model',
            'create:view'
        ];

        $bootstrap = $input->getOption('bootstrap');
        $camel = $input->getOption('camel');
        $doctrine = $input->getOption('doctrine');
        $keep = $input->getOption('keep');
        $lowercase = $input->getOption('lowercase');
        $lowercase = $input->getOption('lowercase');
        $wildfire = $input->getOption('wildfire');

        foreach ($commands as $command) {
            $arguments = [
                'command' => $command,
                'name' => $input->getArgument('name')
            ];

            switch ($command) {
                case 'create:controller':
                    $arguments['--camel'] = $camel;
                    $arguments['--doctrine'] = $doctrine;
                    $arguments['--keep'] = $keep;
                    $arguments['--lowercase'] = $lowercase;
                    $arguments['--wildfire'] = $wildfire;

                    break;
                case 'create:model':
                    $arguments['--camel'] = $camel;
                    $arguments['--doctrine'] = $doctrine;
                    $arguments['--lowercase'] = $lowercase;
                    $arguments['--wildfire'] = $wildfire;

                    break;
                case 'create:view':
                    $arguments['--bootstrap'] = $bootstrap;
                    $arguments['--camel'] = $camel;
                    $arguments['--keep'] = $keep;

                    break;
            }

            $input = new ArrayInput($arguments);
            $application = $this->getApplication()->find($command);
            $application->run($input, $output);
        }
    }
}
