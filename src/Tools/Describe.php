<?php namespace Combustor\Tools;

use Combustor\Tools\Inflect;
use Symfony\Component\Console\Output\OutputInterface;

class Describe {

	private $_columns = array();

	/**
	 * Get the properties and attributes of the specified table
	 * 
	 * @param 	$table
	 * @return 	array
	 */
	public function __construct($table, $output = NULL)
	{
		/**
		 * Load the database configuration from CodeIgniter
		 */

		require APPPATH . 'config/database.php';

		/**
		 * Connect to the database
		 */

		$databaseDriver = ($db['default']['dbdriver'] == 'mysqli') ? 'mysql' : $db['default']['dbdriver'];

		try {
			$databaseHandle = new \PDO(
				$databaseDriver . 
				':host=' . $db['default']['hostname'] . 
				';dbname=' . $db['default']['database'],
				$db['default']['username'],
				$db['default']['password']
			);

			$databaseHandle->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		}
		catch (\PDOException $error) {
			exit($output->writeln('<error>' . $error->getMessage() . '</error>'));
		}

		$tableInformation = $databaseHandle->prepare('DESCRIBE ' . $table);
		$tableInformation->execute();
		$tableInformation->setFetchMode(\PDO::FETCH_OBJ);

		while ($row = $tableInformation->fetch()) {
			$column = array(
				'default'           => $row->Default,
				'extra'             => $row->Extra,
				'field'             => $row->Field,
				'key'               => $row->Key,
				'null'              => $row->Null,
				'referenced_column' => NULL,
				'referenced_table'  => NULL,
				'type'              => $row->Type
			);

			$foreignTableInformation = $databaseHandle->prepare('
				SELECT
					COLUMN_NAME as "column",
					REFERENCED_TABLE_NAME as "referenced_table",
					REFERENCED_COLUMN_NAME as "referenced_column"
				FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
				WHERE TABLE_NAME = "' . $table . '";
			');
			$foreignTableInformation->execute();
			$foreignTableInformation->setFetchMode(\PDO::FETCH_OBJ);

			while ($foreignRow = $foreignTableInformation->fetch()) {
				if ($foreignRow->column == $row->Field) {
					$column['referenced_column'] = $foreignRow->referenced_column;
					$column['referenced_table']  = $foreignRow->referenced_table;
				}
			}

			$this->_columns[] = (object) $column;
		}

		$databaseHandle = NULL;
	}

	/**
	 * Return the result
	 * 
	 * @return array
	 */
	public function result()
	{
		return $this->_columns;
	}

	/**
	 * Get the primary key of the specified table
	 * 
	 * @return string
	 */
	public function getPrimaryKey()
	{
		foreach ($this->_columns as $column) {
			if ($column->key == 'PRI') {
				return $column->field;
			}
		}
	}

}