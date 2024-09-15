<?php

namespace Rougin\Combustor\Packages;

use Rougin\Slytherin\Container\ContainerInterface;
use Rougin\Slytherin\Integration\Configuration;
use Rougin\Slytherin\Integration\IntegrationInterface;
use Rougin\SparkPlug\Instance;

/**
 * @package Combustor
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class SparkplugPackage implements IntegrationInterface
{
    /**
     * @var string|null
     */
    protected $root = null;

    /**
     * @param string|null $root
     */
    public function __construct($root = null)
    {
        $this->root = $root;
    }

    /**
     * @param \Rougin\Slytherin\Container\ContainerInterface $container
     * @param \Rougin\Slytherin\Integration\Configuration    $config
     *
     * @return \Rougin\Slytherin\Container\ContainerInterface
     */
    public function define(ContainerInterface $container, Configuration $config)
    {
        $class = 'Rougin\SparkPlug\Controller';

        return $container->set($class, Instance::create($this->root));
    }
}
