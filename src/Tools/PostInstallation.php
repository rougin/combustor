<?php namespace Rougin\Combustor\Tools;

class PostInstallation {

	/**
	 * Run the installation
	 * 
	 * @return array
	 */
	public function run()
	{
		/**
		 * Get the templates
		 */

		$autoload                = file_get_contents(APPPATH . 'config/autoload.php');
		$codeigniterCore         = file_get_contents(BASEPATH . 'core/CodeIgniter.php');
		$htaccess                = file_get_contents(VENDOR . 'rougin/combustor/src/Templates/Miscellaneous/Htaccess.txt');
		$myPagination            = file_get_contents(VENDOR . 'rougin/combustor/src/Templates/Miscellaneous/Pagination.txt');
		$paginationConfiguration = file_get_contents(VENDOR . 'rougin/combustor/src/Templates/Miscellaneous/PaginationConfiguration.txt');

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
		 * ---------------------------------------------------------------------------------------------
		 * Extended the Pagination class and add a pagination selector in the routes.php
		 * ---------------------------------------------------------------------------------------------
		 */

		if ( ! file_exists(APPPATH . 'libraries/MY_Pagination.php')) {
			$myPaginationFile = fopen(APPPATH . 'libraries/MY_Pagination.php', 'wb');
			
			chmod(APPPATH . 'libraries/MY_Pagination.php', 0777);
			file_put_contents(APPPATH . 'libraries/MY_Pagination.php', $myPagination);
			
			fclose($myPaginationFile);

			$routes = file_get_contents(APPPATH . 'config/routes.php');

			$search  = '$route[\'default_controller\'] = \'welcome\';' . "\n";
			$search .= '$route[\'404_override\'] = \'\';';

			$replace  = '$route[\'default_controller\'] = \'welcome\';' . "\n";
			$replace .= '$route[\'(:any)/page/(:any)\'] = \'$1/index/page/$2\';' . "\n";
			$replace .= '$route[\'(:any)/page\'] = \'$1\';' . "\n";
			$replace .= '$route[\'404_override\'] = \'\';';

			if (strpos($codeigniterCore, 'define(\'CI_VERSION\', \'3.0') !== FALSE) {
				$search  .= "\n" . '$route[\'translate_uri_dashes\'] = FALSE;';
				$replace .= "\n" . '$route[\'translate_uri_dashes\'] = FALSE;';
			}

			$routes = str_replace($search, $replace, $routes);

			file_put_contents(APPPATH . 'config/routes.php', $routes);
		}

		if ( ! file_exists(APPPATH . 'config/pagination.php')) {
			$paginationConfigurationFile = fopen(APPPATH . 'config/pagination.php', 'wb');

			chmod(APPPATH . 'config/pagination.php', 0777);
			file_put_contents(APPPATH . 'config/pagination.php', $paginationConfiguration);

			fclose($paginationConfigurationFile);
		}

		return 0;
	}

}