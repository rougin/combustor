<?php

require 'vendor/autoload.php';

$codeigniter = Rougin\SparkPlug\Instance::create(__DIR__ . '/../build');

require APPPATH . 'config/database.php';

if (file_exists('vendor/rougin/codeigniter/src/helpers/inflector_helper.php')) {
    require 'vendor/rougin/codeigniter/src/helpers/inflector_helper.php';
} else {
    require BASEPATH . 'helpers/inflector_helper.php';
}

$driver   = new Rougin\Describe\Driver\CodeIgniterDriver($db[$active_group]);
$describe = new Rougin\Describe\Describe($driver);
$injector = new Auryn\Injector;

$injector->share($codeigniter)->share($describe);

$combustor = Rougin\Blueprint\Console::boot('combustor.yml', $injector, __DIR__ . '/../build');

$extensions = [ new Rougin\Combustor\Common\InflectorExtension ];
$template   = $combustor->getTemplatePath();

$combustor->setTemplatePath($template, null, $extensions);

$combustor->console->setName('Combustor');
$combustor->console->setVersion('2.0.0');

$combustor->run();
