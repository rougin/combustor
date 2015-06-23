<?php namespace Rougin\Combustor;

class Tools
{

	/**
	 * Get the data type of the specified column
	 * 
	 * @param  string $type
	 * @return string
	 */
	public static function getDataType($type)
	{
		if (strpos($type, 'array') !== FALSE) {
			$type = 'array';
		} else if (strpos($type, 'bigint') !== FALSE) {
			$type = 'bigint';
		} else if (strpos($type, 'blob') !== FALSE) {
			$type = 'blob';
		} else if (strpos($type, 'boolean') !== FALSE) {
			$type = 'boolean';
		} else if (strpos($type, 'datetime') !== FALSE || strpos($type, 'timestamp') !== FALSE) {
			$type = 'datetime';
		} else if (strpos($type, 'datetimetz') !== FALSE) {
			$type = 'datetimetz';
		} else if (strpos($type, 'date') !== FALSE) {
			$type = 'date';
		} else if (strpos($type, 'decimal') !== FALSE || strpos($type, 'double') !== FALSE) {
			$type = 'decimal';
		} else if (strpos($type, 'float') !== FALSE) {
			$type = 'float';
		} else if (strpos($type, 'guid') !== FALSE) {
			$type = 'guid';
		} else if (strpos($type, 'int') !== FALSE) {
			$type = 'integer';
		} else if (strpos($type, 'json_array') !== FALSE) {
			$type = 'json_array';
		} else if (strpos($type, 'object') !== FALSE) {
			$type = 'object';
		} else if (strpos($type, 'simple_array') !== FALSE) {
			$type = 'simple_array';
		} else if (strpos($type, 'smallint') !== FALSE) {
			$type = 'smallint';
		} else if (strpos($type, 'text') !== FALSE) {
			$type = 'text';
		} else if (strpos($type, 'time') !== FALSE) {
			$type = 'time';
		} else if (strpos($type, 'varchar') !== FALSE) {
			$type = 'string';
		}

		return $type;
	}

	/**
	 * Run the post installation process
	 * 
	 * @return integer
	 */
	public static function ignite()
	{
		/**
		 * Get the templates
		 */

		$miscellaneous = VENDOR . 'rougin/combustor/src/Templates/Miscellaneous/';

		$autoload                = file_get_contents(APPPATH . 'config/autoload.php');
		$codeigniterCore         = file_get_contents(BASEPATH . 'core/CodeIgniter.php');
		$htaccess                = file_get_contents($miscellaneous . 'Htaccess.txt');
		$paginationConfiguration = file_get_contents($miscellaneous . 'PaginationConfiguration.txt');

		/**
		 * Add the Composer either in autoload.php (in 3.0dev) or in the index.php
		 */

		if (strpos($codeigniterCore, 'define(\'CI_VERSION\', \'3.0') === FALSE)
		{
			$index = file_get_contents('index.php');
			
			if (strpos($index, 'include_once \'vendor/autoload.php\';') === FALSE)
			{
				$search   = ' * LOAD THE BOOTSTRAP FILE';
				$replace  = ' * LOAD THE COMPOSER AUTOLOAD FILE' . "\n";
				$replace .= ' * --------------------------------------------------------------------' . "\n";
				$replace .= ' */' . "\n";
				$replace .= 'include_once \'vendor/autoload.php\';' . "\n";
				$replace .= '/*' . "\n";
				$replace .= ' * --------------------------------------------------------------------' . "\n";
				$replace .= ' * LOAD THE BOOTSTRAP FILE';

				$index = str_replace($search, $replace, $index);

				$file = fopen('index.php', 'wb');
				file_put_contents('index.php', $index);
				fclose($file);
			}
		}
		else
		{
			$config = file_get_contents('application/config/config.php');

			$search  = '$config[\'composer_autoload\'] = FALSE;';
			$replace = '$config[\'composer_autoload\'] = realpath(\'vendor\') . \'/autoload.php\';';

			$config = str_replace($search, $replace, $config);

			$file = fopen('application/config/config.php', 'wb');
			file_put_contents('application/config/config.php', $config);
			fclose($file);
		}

		/**
		 * Load the url and file helpers and the session library
		 */

		$autoload = file_get_contents(APPPATH . 'config/autoload.php');

		preg_match_all('/\$autoload\[\'libraries\'\] = array\((.*?)\)/', $autoload, $match);
		preg_match_all('/\$autoload\[\'helper\'\] = array\((.*?)\)/', $autoload, $otherMatch);

		$helpers   = explode(', ', end($otherMatch[1]));
		$libraries = explode(', ', end($match[1]));

		if ( ! in_array('\'url\'', $helpers)) {
			array_push($helpers, '\'url\'');
		}

		if ( ! in_array('\'form\'', $helpers)) {
			array_push($helpers, '\'form\'');
		}

		if ( ! in_array('\'session\'', $libraries)) {
			array_push($libraries, '\'session\'');
		}

		$helpers   = array_filter($helpers);
		$libraries = array_filter($libraries);

		$autoload = preg_replace(
			'/\$autoload\[\'libraries\'\] = array\([^)]*\);/',
			'$autoload[\'libraries\'] = array(' . implode(', ', $libraries) . ');',
			$autoload
		);

		$autoload = preg_replace(
			'/\$autoload\[\'helper\'\] = array\([^)]*\);/',
			'$autoload[\'helper\'] = array(' . implode(', ', $helpers) . ');',
			$autoload
		);

		$file = fopen(APPPATH . 'config/autoload.php', 'wb');

		file_put_contents(APPPATH . 'config/autoload.php', $autoload);
		fclose($file);

		/**
		 * Creates .htacess if it does not exists
		 */

		if ( ! file_exists('.htaccess')) {
			$htaccessFile = fopen('.htaccess', 'wb');
			chmod('.htaccess', 0777);

			file_put_contents('.htaccess', $htaccess);
			fclose($htaccessFile);
		}

		/**
		 * Get the contents of config.php
		 */

		$configurationFile = file_get_contents(APPPATH . 'config/config.php');

		$search  = array();
		$replace = array();

		/**
		 * Remove the index.php from $config['index_page']
		 */

		if (strpos($configurationFile, '$config[\'index_page\'] = \'index.php\';') !== FALSE) {
			$search[]  = '$config[\'index_page\'] = \'index.php\';';
			$replace[] = '$config[\'index_page\'] = \'\';';
		}

		/**
		 * Add an encryption key from the configuration
		 */

		if (strpos($configurationFile, '$config[\'encryption_key\'] = \'\';') !== FALSE) {
			$search[]  = '$config[\'encryption_key\'] = \'\';';
			$replace[] = '$config[\'encryption_key\'] = \'' . md5('rougin') . '\';';
		}

		$configurationFile = str_replace($search, $replace, $configurationFile);
		file_put_contents(APPPATH . 'config/config.php', $configurationFile);

		/**
		 * Add an autoload for Pagination Library's configurations in app/config/pagination.php
		 */

		if ( ! file_exists(APPPATH . 'config/pagination.php')) {
			$paginationConfigurationFile = fopen(APPPATH . 'config/pagination.php', 'wb');

			chmod(APPPATH . 'config/pagination.php', 0777);
			file_put_contents(APPPATH . 'config/pagination.php', $paginationConfiguration);

			fclose($paginationConfigurationFile);
		}

		return 0;
	}

	/**
	 * Strip the table schema from the table name
	 * 
	 * @param  string $table
	 * @return string
	 */
	public static function stripTableSchema($table)
	{
		if (strpos($table, '.') !== FALSE) {
			return substr($table, strpos($table, '.') + 1);
		}

		return $table;
	}

	/**
	 * Strip the table schema from the table name
	 * 
	 * @param  string $table
	 * @return string
	 */
	public static function strip_table_schema($table)
	{
		return self::stripTableSchema($table);
	}

}