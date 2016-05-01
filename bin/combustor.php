<?php

// Include the Composer Autoloader
require 'vendor/autoload.php';

// Load the Blueprint library
$injector = new Auryn\Injector;
$console = new Symfony\Component\Console\Application;
$app = new Rougin\Blueprint\Blueprint($console, $injector);

// Application details
$app->console->setName('Combustor');
$app->console->setVersion('1.2.0');

$app
    ->setTemplatePath(__DIR__ . '/../src/Templates')
    ->setCommandPath(__DIR__ . '/../src/Commands')
    ->setCommandNamespace('Rougin\Combustor\Commands');

$app->injector->delegate('CI_Controller', function () {
    return Rougin\SparkPlug\Instance::create();
});

$ci = $app->injector->make('CI_Controller');

$app->injector->delegate('Rougin\Describe\Describe', function () use ($ci) {
    $ci->load->database();
    $ci->load->helper('inflector');

    $config = [];

    $config['default'] = [
        'dbdriver' => $ci->db->dbdriver,
        'hostname' => $ci->db->hostname,
        'username' => $ci->db->username,
        'password' => $ci->db->password,
        'database' => $ci->db->database
    ];

    if (empty($config['default']['hostname'])) {
        $config['default']['hostname'] = $ci->db->dsn;
    }

    $driver = new Rougin\Describe\Driver\CodeIgniterDriver($config);

    return new Rougin\Describe\Describe($driver);
});

// Run the Combustor console application
$app->run();
