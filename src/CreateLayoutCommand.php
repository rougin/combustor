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
			->setDescription('Create a new header and footer file')
			->addOption(
				'bootstrap',
				NULL,
				InputOption::VALUE_NONE,
				'Include the Bootstrap CSS/JS Framework tags'
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
		/**
		 * Load the header and footer template
		 */
		
		$header = file_get_contents(__DIR__ . '/Templates/Views/Layout/Header.txt');
		$footer = file_get_contents(__DIR__ . '/Templates/Views/Layout/Footer.txt');

		$bootstrapContainer = NULL;
		$styleSheets = '<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">';
		$scripts = '</div>';

		if ($input->getOption('bootstrap')) {
			$bootstrapContainer = 'container';
			$styleSheets .= "\n" . '	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">';

			$scripts .= "\n" . '<script src="https://code.jquery.com/jquery-2.1.1.min.js"></script>';
			$scripts .= "\n" . '<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>';
		}

		$search = array(
			'[bootstrapContainer]',
			'[styleSheets]',
			'[scripts]'
		);

		$replace = array(
			$bootstrapContainer,
			$styleSheets,
			$scripts
		);

		$header = str_replace($search, $replace, $header);
		$footer = str_replace($search, $replace, $footer);

		$filepath = APPPATH . 'views/layout/';

		if ( ! @mkdir($filepath, 0777, TRUE)) {
			$output->writeln('<error>The layout directory already exists!</error>');

			exit();
		}

		$headerFile = fopen($filepath . 'header.php', 'wb');
		$footerFile = fopen($filepath . 'footer.php', 'wb');

		file_put_contents($filepath . 'header.php', $header);
		file_put_contents($filepath . 'footer.php', $footer);

		$output->writeln('<info>The layout folder has been created successfully!</info>');
	}

}