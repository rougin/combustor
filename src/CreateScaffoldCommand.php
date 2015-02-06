<?php namespace Combustor;

use Combustor\Tools\Describe;
use Combustor\Tools\Inflect;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateScaffoldCommand extends Command
{

	/**
	 * Set the configurations of the specified command
	 */
	protected function configure()
	{
		$this->setName('create:scaffold')
			->setDescription('Create a new controller, model and view')
			->addArgument(
				'name',
				InputArgument::REQUIRED,
				'Name of the controller, model and view'
			)->addOption(
				'bootstrap',
				NULL,
				InputOption::VALUE_NONE,
				'Include the Bootstrap CSS/JS Framework tags'
			)->addOption(
				'doctrine',
				null,
				InputOption::VALUE_NONE,
				'Create a new controller based on Doctrine'
			)->addOption(
				'keep',
				null,
				InputOption::VALUE_NONE,
				'Keeps the name to be used'
			)->addOption(
				'lowercase',
				null,
				InputOption::VALUE_NONE,
				'Keep the first character of the name to lowercase'
			)->addOption(
				'camel',
				NULL,
				InputOption::VALUE_NONE,
				'Use the camel case naming convention for the accessor and mutators'
			);
	}

	/**
	 * Execute the command
	 * 
	 * @param  InputInterface  $input
	 * @param  OutputInterface $output
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$bootstrap = $input->getOption('bootstrap');
		$camel     = $input->getOption('camel');
		$doctrine  = $input->getOption('doctrine');
		$keep      = $input->getOption('keep');
		$lowercase = $input->getOption('lowercase');
		
		$arguments = array(
			'command' => NULL,
			'name' => $input->getArgument('name')
		);

		$commands = array(
			'create:controller',
			'create:model',
			'create:view'
		);

		foreach ($commands as $command) {
			$arguments['command'] = $command;
			
			if (isset($arguments['--bootstrap'])) {
				unset($arguments['--bootstrap']);
			}

			if (isset($arguments['--camel'])) {
				unset($arguments['--camel']);
			}

			if (isset($arguments['--doctrine'])) {
				unset($arguments['--doctrine']);
			}

			if (isset($arguments['--keep'])) {
				unset($arguments['--keep']);
			}

			if (isset($arguments['--lowercase'])) {
				unset($arguments['--lowercase']);
			}

			if ($command == 'create:controller') {
				$arguments['--camel']     = $camel;
				$arguments['--keep']      = $keep;
				$arguments['--lowercase'] = $lowercase;
			} elseif ($command == 'create:model') {
				$arguments['--camel']     = $camel;
				$arguments['--lowercase'] = $lowercase;
			} elseif ($command == 'create:view') {
				$arguments['--bootstrap'] = $bootstrap;
				$arguments['--camel']     = $camel;
			}

			$input = new ArrayInput($arguments);

			if ($doctrine && ($command == 'create:controller' || $command == 'create:model')) {
				$application = $this->getApplication()->find(str_replace('create', 'doctrine', $command));
			} else {
				$application = $this->getApplication()->find($command);
			}

			$result = $application->run($input, $output);
		}
	}

}