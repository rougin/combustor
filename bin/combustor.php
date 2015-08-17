<?php

/**
 * Define the VENDOR path
 */

define('VENDOR', realpath('vendor') . '/');

/**
 * Include the Composer Autoloader
 */

require VENDOR . 'autoload.php';

/**
 * Load the CodeIgniter instance
 */

$instance = new Rougin\SparkPlug\Instance();

/**
 * Include the Inflector Helper Class from CodeIgniter
 */

require BASEPATH . 'helpers/inflector_helper.php';

/**
 * Load Describe
 */

require APPPATH . 'config/database.php';

$db['default']['driver'] = $db['default']['dbdriver'];
unset($db['default']['dbdriver']);

$describe = new Describe($db['default']);

/**
 * Import the Symfony Components
 */

$application = new Symfony\Component\Console\Application('Combustor', '1.1.2');

if (file_exists(APPPATH . 'views/layout/header.php') && file_exists(APPPATH . 'views/layout/footer.php')) {
    $application->add(new Rougin\Combustor\CreateLayoutCommand());
}

if (file_exists(APPPATH . 'libraries/Wildfire.php')) {
    $application->add(new Rougin\Combustor\Doctrine\RemoveCommand());
} else {
    $application->add(new Rougin\Combustor\Doctrine\InstallCommand());
}

if (file_exists(APPPATH . 'libraries/Doctrine.php')) {
    $application->add(new Rougin\Combustor\Wildfire\RemoveCommand());
} else {
    $application->add(new Rougin\Combustor\Wildfire\InstallCommand());
}

if ($application->has('remove:wildfire') || $application->has('remove:doctrine')) {
    $application->add(new Rougin\Combustor\CreateControllerCommand());
    $application->add(new Rougin\Combustor\CreateModelCommand());
    $application->add(new Rougin\Combustor\CreateScaffoldCommand());
    $application->add(new Rougin\Combustor\CreateViewCommand());
}

$application->run();
