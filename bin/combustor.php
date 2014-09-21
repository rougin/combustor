<?php

/**
 * Include the Composer Autoloader
 */

(@include_once __DIR__ . '/../vendor/autoload.php') || @include_once __DIR__ . '/../../../autoload.php';

define('BASEPATH', str_replace('vendor/rougin/combustor/bin', '', __DIR__ . 'system/'));

define('APPPATH', str_replace('vendor/rougin/combustor/bin', '', __DIR__ . 'application/'));

define('VENDOR', str_replace('rougin/combustor/bin', '', __DIR__));

/**
 * Import the commands from Combuster
 */

use Combustor\CreateControllerCommand;
use Combustor\CreateLayoutCommand;
use Combustor\CreateModelCommand;
use Combustor\CreateScaffoldCommand;
use Combustor\CreateViewCommand;
use Combustor\Doctrine\ModifyCommand;
use Combustor\Doctrine\RevertCommand;

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
$application->add(new ModifyCommand);
$application->add(new RevertCommand);
$application->run();