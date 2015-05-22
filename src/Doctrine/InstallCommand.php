<?php namespace Rougin\Combustor\Doctrine;

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
		$this->setName('install:doctrine')
			->setDescription('Install the Doctrine ORM');
	}

	/**
	 * Execute the command
	 * 
	 * @param  InputInterface  $input
	 * @param  OutputInterface $output
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$composer = file_get_contents('composer.json');
		$composer = str_replace(array('	', "\n", "\r") , array('', '', ''), $composer);

		preg_match_all('/"require": \{(.*?)\}/', $composer, $match);
		$requiredLibraries = explode(',', end($match[1]));

		preg_match_all('/"require-dev": \{(.*?)\}/', $composer, $match);
		$requiredDevLibraries = explode(',', end($match[1]));

		if ( ! in_array('"doctrine/orm": "2.4.*"', $requiredLibraries)) {
			array_push($requiredLibraries, '"doctrine/orm": "2.4.*"');

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

		system('composer update');

		$cli     = file_get_contents(__DIR__ . '/Templates/Cli.txt');
		$library = file_get_contents(__DIR__ . '/Templates/Library.txt');

		/**
		 * ---------------------------------------------------------------------------------------------
		 * Modify the contents of vendor/bin/doctrine.php, create the Doctrine library and create a
		 * "proxies" directory for lazy loading
		 * ---------------------------------------------------------------------------------------------
		 */

		file_put_contents(VENDOR . 'bin/doctrine.php', $cli);
		file_put_contents(VENDOR . 'doctrine/orm/bin/doctrine.php', $cli);

		$file = fopen(APPPATH . 'libraries/Doctrine.php', 'wb');
		file_put_contents(APPPATH . 'libraries/Doctrine.php', $library);
		fclose($file);

		$autoload = file_get_contents(APPPATH . 'config/autoload.php');

		preg_match_all('/\$autoload\[\'libraries\'\] = array\((.*?)\)/', $autoload, $match);

		$libraries = explode(', ', end($match[1]));

		if ( ! in_array('\'doctrine\'', $libraries)) {
			array_push($libraries, '\'doctrine\'');

			if (in_array('\'database\'', $libraries)) {
				$position = array_search('\'database\'', $libraries);

				unset($libraries[$position]);
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

		if ( ! is_dir(APPPATH . 'models/proxies')) {
			mkdir(APPPATH . 'models/proxies');
			chmod(APPPATH . 'models/proxies', 0777);
		}

		/*
		 * ---------------------------------------------------------------------------------------------
		 * Include the Base Model class in Doctrine CLI
		 * ---------------------------------------------------------------------------------------------
		 */

		$abstractCommand = file_get_contents(VENDOR . 'doctrine/orm/lib/Doctrine/ORM/Tools/Console/Command/SchemaTool/AbstractCommand.php');

		$search  = 'use Doctrine\ORM\Tools\SchemaTool;';
		$replace = $search . "\n" . 'include BASEPATH . \'core/Model.php\';';

		$contents = $abstractCommand;

		if (strpos($abstractCommand, 'use Doctrine\ORM\Tools\SchemaTool;') !== FALSE) {
			if (strpos($abstractCommand, 'include BASEPATH . \'core/Model.php\';') === FALSE) {
				$contents = str_replace($search, $replace, $abstractCommand);
			}
		}

		file_put_contents(VENDOR . 'doctrine/orm/lib/Doctrine/ORM/Tools/Console/Command/SchemaTool/AbstractCommand.php', $contents);

		$combustor = file_get_contents(VENDOR . 'rougin/combustor/bin/combustor.php');

		$search = array(
			'// $application->add(new Combustor\Doctrine\RemoveCommand);',
			'$application->add(new Combustor\Doctrine\InstallCommand);'
		);
		$replace = array(
			'$application->add(new Combustor\Doctrine\RemoveCommand);',
			'// $application->add(new Combustor\Doctrine\InstallCommand);'
		);

		$combustor = str_replace($search, $replace, $combustor);

		$file = fopen(VENDOR . 'rougin/combustor/bin/combustor.php', 'wb');

		file_put_contents(VENDOR . 'rougin/combustor/bin/combustor.php', $combustor);
		fclose($file);

		$postInstallation = new PostInstallation();
		$postInstallation->run();

		$output->writeln('<info>The Doctrine ORM is now installed successfully!</info>');
	}

}