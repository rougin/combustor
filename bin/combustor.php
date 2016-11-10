<?php

// Include the Composer Autoloader
require 'vendor/autoload.php';

$codeigniter = Rougin\SparkPlug\Instance::create(__DIR__ . '/../build'); // test

$codeigniter->load->helper('inflector')->database();

$driver   = new Rougin\Describe\Driver\CodeIgniterDriver((array) $codeigniter->db);
$describe = new Rougin\Describe\Describe($driver);

$injector = new Auryn\Injector;

$injector->share($codeigniter)->share($describe);

// Checks the data from combustor.yml
$combustor = Rougin\Blueprint\Console::boot('combustor.yml', $injector);

// ------------------------------------------------------------
// must be set in Blueprint

$twig = $combustor->injector->make('Twig_Environment');

$converter = new Avro\CaseBundle\Util\CaseConverter;
$extension = new Avro\CaseBundle\Twig\Extension\CaseExtension($converter);

// Add extensions
$twig->addExtension($extension);

$combustor->injector->share($twig);
// ------------------------------------------------------------

$combustor->console->setName('Combustor');
$combustor->console->setVersion('2.0.0');

$combustor->run();
