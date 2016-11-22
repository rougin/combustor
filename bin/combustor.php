<?php

require 'vendor/autoload.php';

$directory = __DIR__ . '/../build'; // TEST DIRECTORY

Rougin\SparkPlug\Instance::create($directory);

$basePath = BASEPATH;

require APPPATH . 'config/database.php';

if (is_dir('vendor/rougin/codeigniter/src/')) {
    $basePath = 'vendor/rougin/codeigniter/src/';
}

require $basePath . 'helpers/inflector_helper.php';

$driver   = new Rougin\Describe\Driver\CodeIgniterDriver($db[$active_group]);
$injector = new Auryn\Injector;

$injector->share(new Rougin\Describe\Describe($driver));

$combustor = Rougin\Blueprint\Console::boot('combustor.yml', $injector, $directory);

$extensions = [ new Rougin\Combustor\Common\InflectorExtension ];
$template   = $combustor->getTemplatePath();

$combustor->setTemplatePath($template, null, $extensions);

$combustor->console->setName('Combustor');
$combustor->console->setVersion('2.0.0');

$combustor->run();
