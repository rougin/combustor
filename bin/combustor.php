<?php

/**
 * Include the Composer Autoloader
 */

(@include_once __DIR__ . '/../vendor/autoload.php') || @include_once __DIR__ . '/../../../autoload.php';

define('APPPATH',  __DIR__ . '/../../../../application/');
define('BASEPATH', __DIR__ . '/../../../../system/');
define('VENDOR',   __DIR__ . '/../../../../vendor/');

/**
 * Import the commands from Combuster
 */

use Combustor\CreateControllerCommand;
use Combustor\CreateLayoutCommand;
use Combustor\CreateModelCommand;
use Combustor\CreateScaffoldCommand;
use Combustor\CreateViewCommand;

/**
 * Import the Symfony Console Component
 */

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\HelperSet;

$application = new Application('Combustor', '1');
$application->add(new CreateControllerCommand);
$application->add(new CreateLayoutCommand);
$application->add(new CreateModelCommand);
$application->add(new CreateScaffoldCommand);
$application->add(new CreateViewCommand);
$application->run();