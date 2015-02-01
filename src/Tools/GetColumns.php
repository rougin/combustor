<?php namespace Combustor\Tools;

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
			return exit($output->writeln('<error>Can\'t connect to the database! Please check your configuration at application/config/database.php</error>'));
		}

		mysql_select_db($db['default']['database']);

		/**
		 * If the file is referenced to a table
		 */

		if ( ! $query = mysql_query('DESCRIBE ' . Inflect::pluralize($table))) {
			if ( ! $query = mysql_query('DESCRIBE ' . Inflect::singularize($table))) {
				return exit($output->writeln('<error>There is no table named "' . $table . '" from the database!</error>'));
			}
		}

		while ($row = mysql_fetch_object($query)) {
			$row->Referenced_Column = NULL;
			$row->Referenced_Table = NULL;

			$subQueryString = '
				SELECT
					COLUMN_NAME,
					REFERENCED_TABLE_NAME,
					REFERENCED_COLUMN_NAME
				FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
				WHERE
					TABLE_NAME = "' . $table . '";
			';

			$subQuery = mysql_query($subQueryString);

			while ($foreignKey = mysql_fetch_object($subQuery)) {
				if ($foreignKey->COLUMN_NAME == $row->Field) {
					$row->Referenced_Column = $foreignKey->REFERENCED_COLUMN_NAME;
					$row->Referenced_Table  = $foreignKey->REFERENCED_TABLE_NAME;
				}
			}
			
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