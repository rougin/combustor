<?php

// Include the Composer Autoloader
require 'vendor/autoload.php';

// Load the Blueprint library
$injector = new Auryn\Injector;
$consoleApp = new Symfony\Component\Console\Application;
$app = new Rougin\Blueprint\Blueprint($consoleApp, $injector);

// Application details
$app->console->setName('Combustor');
$app->console->setVersion('1.2.0');

$app
    ->setTemplatePath(__DIR__ . '/../src/Templates')
    ->setCommandPath(__DIR__ . '/../src/Commands')
    ->setCommandNamespace('Rougin\Combustor\Commands');

$app->injector->delegate('CI_Controller', function () {
    $sparkPlug = new Rougin\SparkPlug\SparkPlug($GLOBALS, $_SERVER);

    return $sparkPlug->getCodeIgniter();
});

$ci = $app->injector->make('CI_Controller');

$app->injector->delegate('Rougin\Describe\Describe', function () use ($ci) {
    $ci->load->database();

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
