<?php

/**
 * Define the VENDOR path
 */

define('VENDOR',   realpath('vendor') . '/');

/**
 * Include the Composer Autoloader
 */

require VENDOR . 'autoload.php';

/**
 * Load the CodeIgniter instance
 */

$instance = new Rougin\SparkPlug\Instance();
$codeigniter = $instance->get();

/**
 * Include the Inflector Helper Class from CodeIgniter
 */

require BASEPATH . 'helpers/inflector_helper.php';

/**
 * Import the Symfony Components
 */

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\HelperSet;

$application = new Application('Combustor', '1.2.0');

$application->add(new Rougin\Combustor\CreateLayoutCommand());
$application->add(new Rougin\Combustor\Doctrine\InstallCommand());
// $application->add(new Rougin\Combustor\Doctrine\RemoveCommand());
$application->add(new Rougin\Combustor\Wildfire\InstallCommand());
// $application->add(new Rougin\Combustor\Wildfire\RemoveCommand());

if (class_exists('Rougin\Refinery\MigrateCommand')) {
	$application->add(new Rougin\Refinery\MigrateCommand($codeigniter));
	$application->add(new Rougin\Refinery\MigrateResetCommand($codeigniter));
	$application->add(new Rougin\Refinery\CreateMigrationCommand());
}

if ($application->has('remove:wildfire') || $application->has('remove:doctrine')) {
	$application->add(new Rougin\Combustor\CreateControllerCommand());
	$application->add(new Rougin\Combustor\CreateModelCommand());
	$application->add(new Rougin\Combustor\CreateScaffoldCommand());
	$application->add(new Rougin\Combustor\CreateViewCommand());
}

$application->run();