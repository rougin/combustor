<?php
namespace Combustor;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateModelCommand extends Command
{

	protected function configure()
	{
		$this->setName('create:model')
			->setDescription('Create a new model')
			->addArgument(
				'name',
				InputArgument::REQUIRED,
				'Name of the model'
			)->addOption(
				'keep',
				null,
				InputOption::VALUE_NONE,
				'Keeps the name to be used'
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
	}
	
}