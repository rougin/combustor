<?php

require 'vendor/autoload.php';

$directory = __DIR__ . '/../build'; // test

Rougin\SparkPlug\Instance::create($directory);

require $directory . '/application/config/database.php';

if (file_exists('vendor/rougin/codeigniter/src/helpers/inflector_helper.php')) {
    require 'vendor/rougin/codeigniter/src/helpers/inflector_helper.php';
} else {
    require $directory . '/system/helpers/inflector_helper.php';
}

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
