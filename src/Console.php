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
    protected $file = 'combustor.yml';

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
    public function getAppPath()
    {
        /** @var string */
        $path = realpath($this->root);

        if (! file_exists($path . '/combustor.yml'))
        {
            return $path;
        }

        $parsed = $this->getParsed();

        if (array_key_exists('app_path', $parsed))
        {
            /** @var string */
            $path = $parsed['app_path'];
        }

        /** @var string */
        return realpath($path);
    }

    /**
     * @return string[]
     */
    public function getExcluded()
    {
        $parsed = $this->getParsed();

        $field = 'excluded_fields';

        if (! array_key_exists($field, $parsed))
        {
            return array();
        }

        /** @var string[] */
        return $parsed[$field];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getParsed()
    {
        /** @var string */
        $path = realpath($this->root);

        if (! file_exists($path . '/' . $this->file))
        {
            return array();
        }

        $file = $path . '/' . $this->file;

        /** @var string */
        $file = file_get_contents($file);

        // Replace the constant with root path ----
        $search = '%%CURRENT_DIRECTORY%%';

        $file = str_replace($search, $path, $file);
        // ----------------------------------------

        /** @var array<string, mixed> */
        return Yaml::parse($file);
    }

    /**
     * @return void
     */
    protected function setPackages()
    {
        $container = new Container;

        $path = $this->getAppPath();

        $sparkPlug = new SparkplugPackage($path);
        $container->addPackage($sparkPlug);

        $describe = new DescribePackage;
        $container->addPackage($describe);

        $combustor = new CombustorPackage($path);
        $excluded = $this->getExcluded();

        $combustor->setExcluded($excluded);
        $container->addPackage($combustor);

        $this->setContainer($container);
    }
}
