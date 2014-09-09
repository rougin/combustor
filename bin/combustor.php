<?php

/**
 * Include the Composer Autoloader
 */

(@include_once __DIR__ . '/../vendor/autoload.php') || @include_once __DIR__ . '/../../../autoload.php';

/**
 * Import the commands from Combuster
 */

use Combustor\CreateControllerCommand;
use Combustor\CreateModelCommand;

/**
 * Import the Symfony Console Component
 */

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\HelperSet;

$application = new Application('Combustor', '1');
$application->add(new CreateControllerCommand);
$application->add(new CreateModelCommand);
$application->run();