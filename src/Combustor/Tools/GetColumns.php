<?php

namespace Combustor\Tools;

use Combustor\Tools\Inflect;
use Symfony\Component\Console\Output\OutputInterface;

class GetColumns {

	public $columns = array();

	/**
	 * Get the properties and attributes of the specified table
	 * 
	 * @param 	$table
	 * @return 	array
	 */
	public function __construct($table, $output)
	{
		/**
		 * Load the database configuration from CodeIgniter
		 */

		require APPPATH . 'config/database.php';

		/**
		 * Connect to the database
		 */

		if ( ! @mysql_connect($db['default']['hostname'], $db['default']['username'], $db['default']['password'])) {
			return $output->writeln('<error>Can\'t connect to the database! Please check your configuration at application/config/database.php</error>');
		}

		mysql_select_db($db['default']['database']);

		/**
		 * If the file is referenced to a table
		 */

		if ( ! $query = mysql_query('DESCRIBE ' . Inflect::pluralize($table))) {
			if ( ! $query = mysql_query('DESCRIBE ' . Inflect::singularize($table))) {
				return $output->writeln('<error>There is no table named "' . $table . '" from the database!</error>');
			}
		}

		while ($row = mysql_fetch_object($query)) {
			$this->columns[] = $row;
		}

		return $this->result($this->columns);
	}

	/**
	 * Return the result
	 * 
	 * @return array
	 */
	public function result()
	{
		return $this->columns;
	}

}