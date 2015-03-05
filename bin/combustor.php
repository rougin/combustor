<?php

/**
 * Include the Composer Autoloader
 */

(@include_once __DIR__ . '/../vendor/autoload.php') || @include_once __DIR__ . '/../../../autoload.php';

define('APPPATH',  __DIR__ . '/../../../../application/');
define('BASEPATH', __DIR__ . '/../../../../system/');
define('VENDOR',   __DIR__ . '/../../../../vendor/');

/**
 * Import the Symfony Console Component
 */

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\HelperSet;

$application = new Application('Combustor', '1');

$application->add(new Combustor\InstallCommand);
// $application->add(new Combustor\RemoveCommand);
// $application->add(new Combustor\CreateControllerCommand);
// $application->add(new Combustor\CreateModelCommand);
// $application->add(new Combustor\CreateScaffoldCommand);

$application->add(new Combustor\Doctrine\InstallCommand);
// $application->add(new Combustor\Doctrine\RemoveCommand);
// $application->add(new Combustor\Doctrine\CreateControllerCommand);
// $application->add(new Combustor\Doctrine\CreateModelCommand);
// $application->add(new Combustor\Doctrine\CreateScaffoldCommand);

$application->add(new Combustor\CreateLayoutCommand);
// $application->add(new Combustor\CreateViewCommand);

$application->run();