<?php

namespace Rougin\Combustor\Fixture;

use Auryn\Injector;
use Twig_Environment;
use Twig_Loader_Filesystem;

use Rougin\Describe\Describe;
use Rougin\SparkPlug\SparkPlug;
use Rougin\Describe\Driver\CodeIgniterDriver;

/**
 * Command Builder
 * 
 * @package Combustor
 * @author  Rougin Royce Gutib <rougingutib@gmail.com>
 */
class CommandBuilder
{
    /**
     * Injects a command with its dependencies.
     * 
     * @param  string $command
     * @param  string $app
     * @param  string $templates
     * @return \Symfony\Component\Console\Command\Command
     */
    public static function create($command, $app = '', $templates = '')
    {
        $injector = new Injector;

        if (empty($app)) {
            $app = __DIR__ . '/../TestApp';
        }

        if (empty($templates)) {
            $templates = __DIR__ . '/../../src/Templates';
        }

        $injector->delegate('Twig_Environment', function () use ($templates) {
            $loader = new Twig_Loader_Filesystem($templates);

            return new Twig_Environment($loader);
        });

        $injector->delegate('CI_Controller', function () use ($app) {
            $sparkPlug = new SparkPlug($GLOBALS, $_SERVER, $app);

            return $sparkPlug->getCodeIgniter();
        });

        $describe = 'Rougin\Describe\Describe';

        $injector->delegate($describe, function () use ($injector) {
            $ci = $injector->make('CI_Controller');

            $ci->load->database();
            $ci->load->helper('inflector');

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

            $driver = new CodeIgniterDriver($config);

            return new Describe($driver);
        });

        return $injector->make($command);
    }
}
