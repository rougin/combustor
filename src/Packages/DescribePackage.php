<?php

namespace Rougin\Combustor\Packages;

use Rougin\Describe\Driver\DatabaseDriver;
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

        $interface = 'Rougin\Describe\Driver\DriverInterface';

        $driver = $this->getDriver($config);

        return $container->set($interface, $driver);
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
}
