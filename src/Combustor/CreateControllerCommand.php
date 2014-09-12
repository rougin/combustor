<?php

namespace Combustor;

use Inflect\Inflect;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateControllerCommand extends Command
{

	protected function configure()
	{
		$this->setName('create:controller')
			->setDescription('Create a new controller')
			->addArgument(
				'name',
				InputArgument::REQUIRED,
				'Name of the controller'
			)->addOption(
				'keep',
				null,
				InputOption::VALUE_NONE,
				'Keeps the name to be used'
			)->addOption(
				'empty',
				null,
				InputOption::VALUE_NONE,
				'Generate a controller with no CRUD'
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$name = ($input->getOption('keep')) ? $input->getArgument('name') : Inflect::pluralize($input->getArgument('name'));

		$controller = file_get_contents(__DIR__ . '/Templates/Controller.txt');

		$output->writeln($name);
	}
	
}