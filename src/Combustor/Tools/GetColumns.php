<?php

namespace Combustor\Tools;

use Inflect\Inflect;

class GetColumns {

	public $columns = array();

	/**
	 * Get the properties and attributes of the specified table
	 * 
	 * @param 	$table
	 * @return 	array
	 */
	public function __construct($table)
	{
		/**
		 * Load the database configuration from CodeIgniter
		 */

		require APPPATH . 'config/database.php';

		/**
		 * Connect to the database
		 */

		mysql_connect($db['default']['hostname'], $db['default']['username'], $db['default']['password']);
		mysql_select_db($db['default']['database']);

		/**
		 * If the file is referenced to a table
		 */

		if ( ! $query = mysql_query('DESCRIBE ' . Inflect::pluralize($table))) {
			if ( ! $query = mysql_query('DESCRIBE ' . Inflect::singularize($table))) {
				return $this->result(0);
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
		return ($this->columns) ? $this->columns : 0;
	}

}