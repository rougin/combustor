<?php namespace Combustor;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateLayoutCommand extends Command
{

	/**
	 * Set the configurations of the specified command
	 */
	protected function configure()
	{
		$this->setName('create:layout')
			->setDescription('Create a new header and footer file');
	}

	/**
	 * Execute the command
	 * 
	 * @param  InputInterface  $input
	 * @param  OutputInterface $output
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		/**
		 * Load the header and footer template
		 */
		
		$header = file_get_contents(__DIR__ . '/Templates/Views/Layout/Header.txt');
		$footer = file_get_contents(__DIR__ . '/Templates/Views/Layout/Footer.txt');

		$filepath = APPPATH . 'views/layout/';

		if ( ! @mkdir($filepath, 0777, true)) {
			$output->writeln('<error>The layout directory already exists!</error>');

			exit();
		}

		$header_file = fopen($filepath . 'header.php', 'wb');
		$footer_file = fopen($filepath . 'footer.php', 'wb');

		file_put_contents($filepath . 'header.php', $header);
		file_put_contents($filepath . 'footer.php', $footer);

		$output->writeln('<info>The layout folder has been created successfully!</info>');
	}
	
}