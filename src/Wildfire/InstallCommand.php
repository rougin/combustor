<?php namespace Rougin\Combustor\Wildfire;

use Rougin\Combustor\Tools;
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
		$this->setName('install:wildfire')
			->setDescription('Install Wildfire');
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
		 * Adding Wildfire.php to the "libraries" directory
		 * ---------------------------------------------------------------------------------------------
		 */

		$autoload = file_get_contents(APPPATH . 'config/autoload.php');

		preg_match_all('/\$autoload\[\'libraries\'\] = array\((.*?)\)/', $autoload, $match);

		$libraries = explode(', ', end($match[1]));

		if ( ! in_array('\'wildfire\'', $libraries)) {
			array_push($libraries, '\'wildfire\'');

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

		$file     = fopen(APPPATH . 'libraries/Wildfire.php', 'wb');
		$wildfire = file_get_contents(__DIR__ . '/Templates/Library.txt');

		file_put_contents(APPPATH . 'libraries/Wildfire.php', $wildfire);
		fclose($file);

		$combustor = file_get_contents(VENDOR . 'rougin/combustor/bin/combustor.php');

		$search = array(
			'// $application->add(new Rougin\Combustor\Wildfire\RemoveCommand);',
			'$application->add(new Rougin\Combustor\Wildfire\InstallCommand);',
		);
		$replace = array(
			'$application->add(new Rougin\Combustor\Wildfire\RemoveCommand);',
			'// $application->add(new Rougin\Combustor\Wildfire\InstallCommand);'
		);

		$combustor = str_replace($search, $replace, $combustor);

		$file = fopen(VENDOR . 'rougin/combustor/bin/combustor.php', 'wb');

		file_put_contents(VENDOR . 'rougin/combustor/bin/combustor.php', $combustor);
		fclose($file);

		$output->writeln('<info>Wildfire is now installed successfully!</info>');
	}

}