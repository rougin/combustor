#!/usr/bin/env php
<?php

include_once __DIR__ . '/../../vendor/autoload.php';

use Combustor\CreateControllerCommand;
use Combustor\CreateModelCommand;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\HelperSet;

$application = new Application('Combustor', '1');
$application->add(new CreateControllerCommand);
$application->add(new CreateModelCommand);
$application->run();