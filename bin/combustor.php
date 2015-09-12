<?php

// Define the VENDOR path
$vendor = realpath('vendor');

// Include the Composer Autoloader
require $vendor . '/autoload.php';

$filePath = realpath(__DIR__ . '/../combustor.yml');
$directory = str_replace('/combustor.yml', '', $filePath);

define('BLUEPRINT_FILENAME', $filePath);
define('BLUEPRINT_DIRECTORY', $directory);

// Load the CodeIgniter instance
$instance = new Rougin\SparkPlug\Instance();

// Include the Inflector helper from CodeIgniter
require BASEPATH . 'helpers/inflector_helper.php';

// Load the Blueprint library
$blueprint = include($vendor . '/rougin/blueprint/bin/blueprint.php');

if ($blueprint->hasError) {
    exit($blueprint->showError());
}

$blueprint->console->setName('Combustor');
$blueprint->console->setVersion('1.1.3');

// Run the Combustor console application
$blueprint->console->run();