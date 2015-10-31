<?php

// Load the Blueprint library
$combustor = new Rougin\Blueprint\Blueprint(
    new Symfony\Component\Console\Application,
    new Auryn\Injector
);

$combustor
    ->setTemplatePath(__DIR__ . '/../src/Templates')
    ->setCommandPath(__DIR__ . '/../src/Commands')
    ->setCommandNamespace('Rougin\Combustor\Commands');

$combustor->console->setName('Combustor');
$combustor->console->setVersion('1.1.4');

$combustor->injector->delegate('CI_Controller', function () {
    $sparkPlug = new Rougin\SparkPlug\SparkPlug($GLOBALS, $_SERVER);

    return $sparkPlug->getCodeIgniter();
});

$combustor->injector->delegate('Rougin\Describe\Describe', function () use ($db) {
    return new Rougin\Describe\Describe(
        new Rougin\Describe\Driver\CodeIgniterDriver($db)
    );
});

// Run the Combustor console application
$combustor->run();
