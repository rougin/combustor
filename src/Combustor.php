<?php

namespace Rougin\Combustor;

/**
 * Combustor Console
 *
 * @package Combustor
 * @author  Rougin Royce Gutib <rougingutib@gmail.com>
 */
class Combustor extends \Rougin\Blueprint\Console
{
    /**
     * @var \Rougin\Blueprint\Blueprint
     */
    protected static $application;

    /**
     * @var string
     */
    protected static $name = 'Combustor';

    /**
     * @var string
     */
    protected static $version = '2.0.0';

    /**
     * Prepares the console application.
     *
     * @param  string               $filename
     * @param  \Auryn\Injector|null $injector
     * @param  string|null          $directory
     * @return \Rougin\Blueprint\Blueprint
     */
    public static function boot($filename = 'combustor.yml', \Auryn\Injector $injector = null, $directory = null)
    {
        \Rougin\SparkPlug\Instance::create($directory);

        self::$application = parent::boot($filename, $injector, $directory);

        self::prepareDependencies();
        self::prepareTemplates();

        self::$application->setCommandPath(__DIR__ . DIRECTORY_SEPARATOR . 'Commands');
        self::$application->setCommandNamespace('Rougin\Combustor\Commands');

        return self::$application;
    }

    /**
     * Prepares the dependencies to be used.
     *
     * @return void
     */
    protected static function prepareDependencies()
    {
        $basePath = BASEPATH;

        require APPPATH . 'config/database.php';

        if (is_dir('vendor/rougin/codeigniter/src/')) {
            $basePath = 'vendor/rougin/codeigniter/src/';
        }

        require $basePath . 'helpers/inflector_helper.php';

        $driver = new \Rougin\Describe\Driver\CodeIgniterDriver($db[$active_group]);

        self::$application->injector->share(new \Rougin\Describe\Describe($driver));
    }

    /**
     * Prepares the templates to be used.
     *
     * @return void
     */
    protected static function prepareTemplates()
    {
        $extensions = [ new \Rougin\Combustor\Common\InflectorExtension ];

        $template = self::$application->getTemplatePath();

        self::$application->setTemplatePath($template, null, $extensions);
    }
}
