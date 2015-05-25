<?php

/**
 * Include the Composer Autoloader
 */

(@include_once __DIR__ . '/../vendor/autoload.php') || @include_once __DIR__ . '/../../../autoload.php';

define('APPPATH',  __DIR__ . '/../../../../application/');
define('BASEPATH', __DIR__ . '/../../../../system/');
define('VENDOR',   __DIR__ . '/../../../../vendor/');

/**
 * Include the Inflector Helper Class from CodeIgniter
 */

require BASEPATH . 'helpers/inflector_helper.php';

/**
 * Import the Symfony Console Component
 */

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\HelperSet;

$application = new Application('Combustor', '1');

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

$application->add(new Rougin\Combustor\CreateLayoutCommand);

$application->run();