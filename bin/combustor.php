<?php

/**
 * Define the APPPATH, VENDOR, and BASEPATH paths
 */

define('APPPATH',  realpath('application') . '/');
define('ICONV_ENABLED', extension_loaded('iconv') ? TRUE : FALSE);
define('MB_ENABLED', extension_loaded('mbstring') ? TRUE : FALSE);
define('VENDOR',   realpath('vendor') . '/');

$directory = new RecursiveDirectoryIterator(getcwd(), FilesystemIterator::SKIP_DOTS);
foreach (new RecursiveIteratorIterator($directory, RecursiveIteratorIterator::SELF_FIRST) as $path) {
	if (strpos($path->__toString(), 'core/CodeIgniter.php') !== FALSE) {
		$basepath = str_replace('core/CodeIgniter.php', '', $path->__toString());
		define('BASEPATH', $basepath);

		break;
	}
}

/**
 * Include the Composer Autoloader
 */

require VENDOR . 'autoload.php';

/**
 * Include the Inflector Helper Class from CodeIgniter
 */

require BASEPATH . 'helpers/inflector_helper.php';

/**
 * Import the Symfony Components
 */

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\HelperSet;

$application = new Application('Combustor', '1.1.0');

$application->add(new Rougin\Combustor\CreateLayoutCommand);
$application->add(new Rougin\Combustor\Doctrine\InstallCommand);
// $application->add(new Rougin\Combustor\Doctrine\RemoveCommand);
$application->add(new Rougin\Combustor\Wildfire\InstallCommand);
// $application->add(new Rougin\Combustor\Wildfire\RemoveCommand);

if ($application->has('remove:wildfire') || $application->has('remove:doctrine')) {
	$application->add(new Rougin\Combustor\CreateControllerCommand);
	$application->add(new Rougin\Combustor\CreateModelCommand);
	$application->add(new Rougin\Combustor\CreateScaffoldCommand);
	$application->add(new Rougin\Combustor\CreateViewCommand);
}

$application->run();