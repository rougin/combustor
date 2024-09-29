<?php

namespace Rougin\Combustor;

use Rougin\Blueprint\Blueprint;
use Rougin\Blueprint\Container;
use Rougin\Combustor\Packages\CombustorPackage;
use Rougin\Combustor\Packages\DescribePackage;
use Rougin\Combustor\Packages\SparkplugPackage;
use Symfony\Component\Yaml\Yaml;

/**
 * @package Combustor
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class Console extends Blueprint
{
    /**
     * @var string
     */
    protected $name = 'Combustor';

    /**
     * @var string
     */
    protected $root;

    /**
     * @var string
     */
    protected $version = '1.3.0';

    /**
     * @param string $root
     */
    public function __construct($root)
    {
        $namespace = __NAMESPACE__ . '\Commands';

        $this->setCommandNamespace($namespace);

        $this->setCommandPath(__DIR__ . '/Commands');

        $this->root = $root;

        $this->setPackages();
    }

    /**
     * @return string
     */
    protected function getAppPath()
    {
        /** @var string */
        $path = realpath($this->root);

        if (! file_exists($path . '/combustor.yml'))
        {
            return $path;
        }

        $file = $path . '/combustor.yml';

        /** @var string */
        $file = file_get_contents($file);

        // Replace the constant with root path ----
        $search = '%%CURRENT_DIRECTORY%%';

        $file = str_replace($search, $path, $file);
        // ----------------------------------------

        /** @var array<string, string> */
        $parsed = Yaml::parse($file);

        if (array_key_exists('app_path', $parsed))
        {
            /** @var string */
            $path = realpath($parsed['app_path']);
        }

        return $path;
    }

    /**
     * @return void
     */
    protected function setPackages()
    {
        $container = new Container;

        $path = $this->getAppPath();

        $container->addPackage(new SparkplugPackage($path));
        $container->addPackage(new DescribePackage);
        $container->addPackage(new CombustorPackage($path));

        $this->setContainer($container);
    }
}
