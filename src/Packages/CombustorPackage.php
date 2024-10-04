<?php

namespace Rougin\Combustor\Packages;

use Rougin\Combustor\Combustor;
use Rougin\Slytherin\Container\ContainerInterface;
use Rougin\Slytherin\Integration\Configuration;
use Rougin\Slytherin\Integration\IntegrationInterface;

/**
 * @package Combustor
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class CombustorPackage implements IntegrationInterface
{
    /**
     * @var string[]
     */
    protected $excluded = array();

    /**
     * @var string
     */
    protected $root;

    /**
     * @param string $root
     */
    public function __construct($root)
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
        $app = new Combustor($this->root);

        if ($this->excluded)
        {
            $app->setExcluded($this->excluded);
        }

        $name = 'Rougin\SparkPlug\Controller';

        if ($container->has($name))
        {
            /** @var \Rougin\SparkPlug\Controller */
            $class = $container->get($name);

            $app->setApp($class);
        }

        $name = 'Rougin\Describe\Driver\DriverInterface';

        if ($container->has($name))
        {
            /** @var \Rougin\Describe\Driver\DriverInterface */
            $class = $container->get($name);

            $app->setDriver($class);
        }

        return $container->set(get_class($app), $app);
    }

    /**
     * @param string[] $excluded
     *
     * @return self
     */
    public function setExcluded($excluded)
    {
        $this->excluded = $excluded;

        return $this;
    }
}
