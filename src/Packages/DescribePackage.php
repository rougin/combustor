<?php

namespace Rougin\Combustor\Packages;

use Rougin\Describe\Driver\DatabaseDriver;
use Rougin\Slytherin\Container\ContainerInterface;
use Rougin\Slytherin\Integration\Configuration;
use Rougin\Slytherin\Integration\IntegrationInterface;

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
        $class = 'Rougin\SparkPlug\Controller';

        if (! $container->has($class))
        {
            return $container;
        }

        /** @var \Rougin\SparkPlug\Controller */
        $ci = $container->get($class);

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

        $interface = 'Rougin\Describe\Driver\DriverInterface';

        $name = $config['dbdriver'];

        $driver = new DatabaseDriver($name, $config);

        return $container->set($interface, $driver);
    }
}
