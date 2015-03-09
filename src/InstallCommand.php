<?php namespace Combustor;

use Combustor\Tools\PostInstallation;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class InstallCommand extends Command
{

	/**
	 * Set the configurations of the specified command
	 */
	protected function configure()
	{
		$this->setName('install:factory')
			->setDescription('Install the customized factory pattern');
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

		$autoload = file_get_contents(APPPATH . 'config/autoload.php');

		preg_match_all('/\$autoload\[\'libraries\'\] = array\((.*?)\)/', $autoload, $match);

		$libraries = explode(', ', end($match[1]));

		if ( ! in_array('\'factory\'', $libraries)) {
			array_push($libraries, '\'factory\'');

			if ( ! in_array('\'database\'', $libraries)) {
				array_push($libraries, '\'database\'');
			}

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

		$factory = file_get_contents(__DIR__ . '/Templates/Factory.txt');
		$file    = fopen(APPPATH . 'libraries/Factory.php', 'wb');

		file_put_contents(APPPATH . 'libraries/Factory.php', $factory);
		fclose($file);

		$combustor = file_get_contents(VENDOR . 'rougin/combustor/bin/combustor.php');

		if (strpos($combustor, '// $application->add(new Combustor\CreateControllerCommand);') !== FALSE) {
			$search = array(
'// $application->add(new Combustor\CreateControllerCommand);
// $application->add(new Combustor\CreateModelCommand);
// $application->add(new Combustor\CreateScaffoldCommand);',
				'// $application->add(new Combustor\RemoveCommand);',
				'$application->add(new Combustor\InstallCommand);',
			);
			$replace = array(
'$application->add(new Combustor\CreateControllerCommand);
$application->add(new Combustor\CreateModelCommand);
$application->add(new Combustor\CreateScaffoldCommand);',
				'$application->add(new Combustor\RemoveCommand);',
				'// $application->add(new Combustor\InstallCommand);'
			);

			$combustor = str_replace($search, $replace, $combustor);

			$createViewCommandExists = strpos($combustor, '// $application->add(new Combustor\CreateViewCommand);') !== FALSE;
			$doctrineIsNotInstalled  = ! file_exists(APPPATH . 'libraries/Doctrine.php');

			if ($createViewCommandExists && $doctrineIsNotInstalled) {
				$search  = '// $application->add(new Combustor\CreateViewCommand);';
				$replace = '$application->add(new Combustor\CreateViewCommand);';

				$combustor = str_replace($search, $replace, $combustor);
			}

			$file = fopen(VENDOR . 'rougin/combustor/bin/combustor.php', 'wb');

			file_put_contents(VENDOR . 'rougin/combustor/bin/combustor.php', $combustor);
			fclose($file);
		}

		$postInstallation = new PostInstallation();
		$postInstallation->run();

		$output->writeln('<info>The customized factory pattern is now installed successfully!</info>');
	}

}