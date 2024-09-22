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
        $class = 'Rougin\SparkPlug\Controller';

        // If cannot determine APPPATH, ignore ---
        $appPath = $this->root . '/application';

        $root = $this->root . '/config';

        if (! is_dir($appPath) && ! is_dir($root))
        {
            // @codeCoverageIgnoreStart
            return $container;
            // @codeCoverageIgnoreEnd
        }
        // ---------------------------------------

        $app = Instance::create($this->root);

        return $container->set($class, $app);
    }
}
