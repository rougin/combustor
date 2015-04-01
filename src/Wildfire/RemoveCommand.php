<?php namespace Combustor\Wildfire;

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
		$this->setName('remove:wildfire')
			->setDescription('Remove Wildfire');
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

		if ( ! file_exists(APPPATH . 'libraries/Wildfire.php')) {
			exit($output->writeln('<error>Wildfire is not installed!</error>'));
		}

		$autoload = file_get_contents(APPPATH . 'config/autoload.php');

		preg_match_all('/\$autoload\[\'libraries\'\] = array\((.*?)\)/', $autoload, $match);

		$libraries = explode(', ', end($match[1]));

		if (in_array('\'wildfire\'', $libraries)) {
			$position = array_search('\'wildfire\'', $libraries);

			unset($libraries[$position]);

			if ( ! in_array('\'database\'', $libraries)) {
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

		$combustor = file_get_contents(VENDOR . 'rougin/combustor/bin/combustor.php');

		$search = array(
			'$application->add(new Combustor\Wildfire\RemoveCommand);',
			'// $application->add(new Combustor\Wildfire\InstallCommand);'
		);
		$replace = array(
			'// $application->add(new Combustor\Wildfire\RemoveCommand);',
			'$application->add(new Combustor\Wildfire\InstallCommand);'
		);

		$combustor = str_replace($search, $replace, $combustor);

		$file = fopen(VENDOR . 'rougin/combustor/bin/combustor.php', 'wb');

		file_put_contents(VENDOR . 'rougin/combustor/bin/combustor.php', $combustor);
		fclose($file);

		if (unlink(APPPATH . 'libraries/Wildfire.php')) {
			$output->writeln('<info>Wildfire is now successfully removed!</info>');
		} else {
			$output->writeln('<error>There\'s something wrong while removing. Please try again later.</error>');
		}
	}

}