<?php namespace Combustor;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RemoveDoctrineCommand extends Command
{

	/**
	 * Set the configurations of the specified command
	 */
	protected function configure()
	{
		$this->setName('remove:doctrine')
			->setDescription('Remove the Doctrine ORM');
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
		 * ---------------------------------------------------------------------------------------------
		 * Adding the Doctrine.php to the "libraries" directory
		 * ---------------------------------------------------------------------------------------------
		 */

		if ( ! file_exists(APPPATH . 'libraries/Doctrine.php')) {
			exit($output->writeln('<error>The Doctrine ORM is not installed!</error>'));
		}

		$composer = file_get_contents('composer.json');
		$composer = str_replace(array('	', "\n", "\r") , array('', '', ''), $composer);

		preg_match_all('/"require": \{(.*?)\}/', $composer, $match);
		$requiredLibraries = explode(',', end($match[1]));

		preg_match_all('/"require-dev": \{(.*?)\}/', $composer, $match);
		$requiredDevLibraries = explode(',', end($match[1]));

		if ( ! in_array('"doctrine/orm": "2.4.*"', $requiredLibraries)) {
			$position = array_search('"doctrine/orm": "2.4.*"', $requiredLibraries);

			unset($requiredLibraries[$position]);

			$composer =
'{
	"description" : "The CodeIgniter framework",
	"name" : "codeigniter/framework",
	"license": "MIT",
	"require": {' . "\n\t\t" . implode(',' . "\n\t\t", $requiredLibraries) . "\n\t" . '},
	"require-dev": {' . "\n\t\t" . implode(',' . "\n\t\t", $requiredDevLibraries) . "\n\t" . '}
}';

			$file = fopen('composer.json', 'wb');

			file_put_contents('composer.json', $composer);
			fclose($file);
		}

		if ( ! rmdir(VENDOR . 'doctrine')) {
			$output->writeln('<error>There\'s something wrong while removing. Please try again later.</error>');
		}

		system('composer update');

		if (unlink(APPPATH . 'libraries/Doctrine.php')) {
			$output->writeln('<info>The Doctrine ORM is now successfully removed!</info>');
		} else {
			$output->writeln('<error>There\'s something wrong while removing. Please try again later.</error>');
		}
	}

}