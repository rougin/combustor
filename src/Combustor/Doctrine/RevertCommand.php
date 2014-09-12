<?php

namespace Combustor\Doctrine;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Doctrine\ModifyCommand Controller Class
 *
 * @author 		Rougin Gutib
 */

class RevertCommand extends Command
{

	protected function configure()
	{
		$this->setName('doctrine:revert')
			->setDescription('Return to the Doctrine 2 ORM to its original settings');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		/**
		 * Retrieve the following files
		 */
		
		$entityGenerator = VENDOR . 'doctrine/orm/lib/Doctrine/ORM/Tools/EntityGenerator.php';
		$inflector = VENDOR . 'doctrine/inflector/lib/Doctrine/Common/Inflector/Inflector.php';

		if ( ! file_exists($entityGenerator) &&  ! file_exists($inflector)) {
			$output->writeln('<error>The files that you want to be modified cannot be found.</error>');

			exit();
		} else {
			$entityGenerator_file = file_get_contents($entityGenerator);
			$inflector_file = file_get_contents($inflector);
		}

		$search = array(
			'$methodName = $type . \'_\' . strtolower(Inflector::classify($fieldName));',
			'* Convert a word in to the format for a Doctrine class name',
			'preg_match_all(\'!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!\', $word, $matches);

		$ret = $matches[0];
		
		foreach ($ret as &$match) {
			$match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
		}

		return implode(\'_\', $ret);'
		);
		$replace = array(
			'$methodName = $type . Inflector::classify($fieldName);',
			'* Convert a word in to the format for a Doctrine class name. Converts \'table_name\' to \'TableName\'',
			'return str_replace(" ", "", ucwords(strtr($word, "_-", "  ")))'
		);

		/**
		 * Search and replace the following keywords
		 */

		$output->writeln('<yellow>Modifying the Doctrine\ORM\Tools\EntityGenerator.php...</yellow>');
		$entityGenerator_file = str_replace($search, $replace, $entityGenerator_file);

		$output->writeln('<yellow>Modifying the Doctrine\Common\Inflector.php...</yellow>');
		$inflector_file = str_replace($search, $replace, $inflector_file);

		if (file_put_contents($entityGenerator, $entityGenerator_file) && file_put_contents($inflector, $inflector_file)) {
			$output->writeln('<info>The core has been reverted back to its original state!</info>');
		} else {
			$output->writeln('<error>Oops! Somethings wrong while modifying the file.</error>');
		}
	}
	
}