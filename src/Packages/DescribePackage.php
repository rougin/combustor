<?php

namespace Rougin\Combustor\Packages;

use Rougin\Combustor\Commands\CreateController;
use Rougin\Describe\Driver\DatabaseDriver;
use Rougin\Describe\Driver\DriverInterface;
use Rougin\Slytherin\Container\ContainerInterface;
use Rougin\Slytherin\Integration\Configuration;
use Rougin\Slytherin\Integration\IntegrationInterface;
use Rougin\SparkPlug\Controller;

/**
 * @package Combustor
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class DescribePackage implements IntegrationInterface
{
    /**
     * @param \Rougin\Slytherin\Container\ContainerInterface $container
     * @param \Rougin\Slytherin\Integration\Configuration    $config
     *
     * @return \Rougin\Slytherin\Container\ContainerInterface
     */
    public function define(ContainerInterface $container, Configuration $config)
    {
        // Ignore if Spark Plug is not defined ---
        $ci3Ctrl = 'Rougin\SparkPlug\Controller';

        if (! $container->has($ci3Ctrl))
        {
            return $container;
        }
        // ---------------------------------------

        /** @var \Rougin\SparkPlug\Controller */
        $ci3App = $container->get($ci3Ctrl);

        $config = $this->getConfig($ci3App);

        // TODO: No need to add DriverInterface per Command ---
        $driver = $this->getDriver($config);

        $commands = $this->setCommands($driver);

        foreach ($commands as $item)
        {
            $container->set(get_class($item), $item);
        }
        // ----------------------------------------------------

        return $container;
    }

    /**
     * @param \Rougin\SparkPlug\Controller $ci
     *
     * @return array<string, string>
     */
    protected function getConfig(Controller $ci)
    {
        $ci->load->database();
        $ci->load->helper('inflector');

        $config = array();

        $config['dbdriver'] = $ci->db->dbdriver;
        $config['hostname'] = $ci->db->hostname;
        $config['username'] = $ci->db->username;
        $config['password'] = $ci->db->password;
        $config['database'] = $ci->db->database;

        if (empty($config['hostname']))
        {
            $config['hostname'] = $ci->db->dsn;
        };

        return $config;
    }

    /**
     * @param array<string, string> $config
     *
     * @return \Rougin\Describe\Driver\DriverInterface
     */
    protected function getDriver($config)
    {
        return new DatabaseDriver($config['dbdriver'], $config);
    }

    /**
     * @param \Rougin\Describe\Driver\DriverInterface $driver
     *
     * @return \Rougin\Combustor\Command[]
     */
    protected function setCommands(DriverInterface $driver)
    {
        $commands = array();

        $commands[] = new CreateController($driver);

        return $commands;
    }
}
