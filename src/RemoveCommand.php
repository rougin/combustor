<?php namespace Combustor;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RemoveCommand extends Command
{

	/**
	 * Set the configurations of the specified command
	 */
	protected function configure()
	{
		$this->setName('remove:factory')
			->setDescription('Remove the customized factory pattern');
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
		 * Adding the Factory.php to the "libraries" directory
		 * ---------------------------------------------------------------------------------------------
		 */

		if ( ! file_exists(APPPATH . 'libraries/Factory.php')) {
			exit($output->writeln('<error>The customized factory pattern is not installed!</error>'));
		}

		$autoload = file_get_contents(APPPATH . 'config/autoload.php');

		preg_match_all('/\$autoload\[\'libraries\'\] = array\((.*?)\)/', $autoload, $match);

		$libraries = explode(', ', end($match[1]));

		if (in_array('\'factory\'', $libraries)) {
			$position = array_search('\'factory\'', $libraries);

			unset($libraries[$position]);

			$libraries = array_filter($libraries);

			$autoload = preg_replace(
				'/\$autoload\[\'libraries\'\] = array\([^)]*\);/',
				'$autoload[\'libraries\'] = array(' . implode(', ', $libraries) . ');',
				$autoload
			);

			$file = fopen(APPPATH . 'config/autoload.php', 'wb');

			file_put_contents(APPPATH . 'config/autoload.php', $autoload);
			fclose($file);
		}

		$combustor = file_get_contents(VENDOR . 'rougin/combustor/bin/combustor');

		$commandsExists = strpos($combustor, '$application->add(new Combustor\CreateControllerCommand);') !== FALSE;
		$doctrineIsNotInstalled = ! file_exists(APPPATH . 'libraries/Doctrine.php');

		if ($commandsExists && $doctrineIsNotInstalled) {
			$search = array(
'$application->add(new Combustor\CreateControllerCommand);
$application->add(new Combustor\CreateModelCommand);
$application->add(new Combustor\CreateScaffoldCommand);',
				'$application->add(new Combustor\RemoveCommand);',
				'// $application->add(new Combustor\InstallCommand);'
			);
			$replace = array(
'// $application->add(new Combustor\CreateControllerCommand);
// $application->add(new Combustor\CreateModelCommand);
// $application->add(new Combustor\CreateScaffoldCommand);',
				'// $application->add(new Combustor\RemoveCommand);',
				'$application->add(new Combustor\InstallCommand);'
			);

			$combustor = str_replace($search, $replace, $combustor);

			$createViewCommandExists = strpos($combustor, '$application->add(new Combustor\CreateViewCommand);') !== FALSE;

			if ($createViewCommandExists && $doctrineIsNotInstalled) {
				$search  = '$application->add(new Combustor\CreateViewCommand);';
				$replace = '// $application->add(new Combustor\CreateViewCommand);';

				$combustor = str_replace($search, $replace, $combustor);
			}

			$file = fopen(VENDOR . 'rougin/combustor/bin/combustor', 'wb');

			file_put_contents(VENDOR . 'rougin/combustor/bin/combustor', $combustor);
			fclose($file);
		}

		if (unlink(APPPATH . 'libraries/Factory.php')) {
			$output->writeln('<info>The customized factory pattern is now successfully removed!</info>');
		} else {
			$output->writeln('<error>There\'s something wrong while removing. Please try again later.</error>');
		}
	}

}