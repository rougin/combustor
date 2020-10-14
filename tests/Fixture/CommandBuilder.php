<?php

namespace Rougin\Combustor\Fixture;

use Auryn\Injector;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

use Rougin\Describe\Describe;
use Rougin\SparkPlug\Instance;
use Rougin\Describe\Driver\CodeIgniterDriver;

/**
 * Command Builder
 *
 * @package Combustor
 * @author  Rougin Gutib <rougingutib@gmail.com>
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

        if (empty($app))
        {
            $app = __DIR__ . '/../TestApp';
        }

        if (empty($templates))
        {
            $templates = __DIR__ . '/../../src/Templates';
        }

        $injector->delegate('Twig\Environment', function () use ($templates) {
            $loader = new FilesystemLoader($templates);

            return new Environment($loader);
        });

        $injector->delegate('Rougin\Describe\Describe', function () use ($app) {
            $ci = Instance::create($app);

            $ci->load->database();
            $ci->load->helper('inflector');

            $config['default'] =
            [
                'dbdriver' => $ci->db->dbdriver,
                'hostname' => $ci->db->hostname,
                'username' => $ci->db->username,
                'password' => $ci->db->password,
                'database' => $ci->db->database
            ];

            if (empty($config['default']['hostname']))
            {
                $config['default']['hostname'] = $ci->db->dsn;
            }

            $driver = new CodeIgniterDriver($config);

            return new Describe($driver);
        });

        return $injector->make($command);
    }
}
