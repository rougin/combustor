<?php

namespace Rougin\Combustor;

use Rougin\Blueprint\Command as Blueprint;
use Rougin\Classidy\Generator;
use Rougin\Combustor\Template\Controller;
use Rougin\Combustor\Template\Doctrine\Model as DoctrineModel;
use Rougin\Combustor\Template\Wildfire\Model as WildfireModel;
use Rougin\Describe\Driver\DriverInterface;

/**
 * @package Combustor
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class Command extends Blueprint
{
    const TYPE_WILDFIRE = 0;

    const TYPE_DOCTRINE = 1;

    /**
     * @var \Rougin\Describe\Driver\DriverInterface
     */
    protected $driver;

    /**
     * @var \Rougin\Classidy\Generator
     */
    protected $maker;

    /**
     * @var \Rougin\Combustor\Location
     */
    protected $path;

    /**
     * @param \Rougin\Describe\Driver\DriverInterface $driver
     * @param \Rougin\Classidy\Generator              $maker
     * @param \Rougin\Combustor\Location              $path
     */
    public function __construct(DriverInterface $driver, Generator $maker, Location $path)
    {
        $this->driver = $driver;

        $this->path = $path;

        $this->maker = $maker;
    }

    /**
     * Configures the current command.
     *
     * @return void
     */
    public function init()
    {
        $this->addArgument('name', 'Name of the table');

        // TODO: Add Bootstrap styling in the generated views -------------
        // if ($this->name === 'create:layout')
        // {
        //     $this->addOption('bootstrap', 'Includes Bootstrap styling');
        // }
        // ----------------------------------------------------------------

        $this->addOption('camel', 'Uses the camelCase convention');

        $this->addOption('keep', 'Keeps the name to be used');

        if ($this->name === 'create:controller')
        {
            $this->addOption('lowercase', 'Keeps the first character of the name to lowercase');
        }
    }

    /**
     * Executes the command.
     *
     * @return integer
     */
    public function run()
    {
        try
        {
            $type = $this->getInstalled();
        }
        catch (\Exception $e)
        {
            $this->showFail($e->getMessage());

            return self::RETURN_FAILURE;
        }

        /** @var string */
        $table = $this->getArgument('name');

        $plate = $this->getTemplate($table, $type);

        $plate = $this->maker->make($plate);

        $root = $this->path->getAppPath();

        if ($this->name === 'create:controller')
        {
            $name = ucfirst(Inflector::plural($table));
            $file = $root . '/controllers/' . $name . '.php';
            file_put_contents($file, $plate);
        }

        if ($this->name === 'create:model')
        {
            $name = ucfirst(Inflector::singular($table));
            $file = $root . '/models/' . $name . '.php';
            file_put_contents($file, $plate);
        }

        $this->showPass('Controller successfully created!');

        return self::RETURN_SUCCESS;
    }

    /**
     * @return integer
     * @throws \Exception
     */
    protected function getInstalled()
    {
        /** @var boolean */
        $doctrine = $this->getOption('doctrine');
        $class = 'Rougin\Credo\Credo';
        $doctrineExists = class_exists($class);

        /** @var boolean */
        $wildfire = $this->getOption('wildfire');
        $class = 'Rougin\Wildfire\Wildfire';
        $wildfireExists = class_exists($class);

        /**
         * If --doctrine or --wildfire not specified
         */
        if (! $doctrine && ! $wildfire)
        {
            /**
             * If not specified as option and packages are not yet installed
             */
            if (! $doctrineExists && ! $wildfireExists)
            {
                $text = 'Both "rougin/credo" and "rougin/wildfire" are not installed.';

                throw new \Exception($text . ' Kindly "rougin/credo" or "rougin/wildfire" first.');
            }

            /**
             * If both installed and not specified as option
             */
            if ($doctrineExists && $wildfireExists)
            {
                $text = 'Both "rougin/credo" and "rougin/wildfire" are installed.';

                throw new \Exception($text . ' Kindly select --doctrine or --wildfire first.');
            }
        }

        if ($doctrine || $doctrineExists)
        {
            return self::TYPE_DOCTRINE;
        }

        return self::TYPE_WILDFIRE;
    }

    /**
     * @param string  $table
     * @param integer $type
     *
     * @return \Rougin\Classidy\Classidy
     */
    protected function getTemplate($table, $type)
    {
        $isModel = $this->name === 'create:model';

        if ($isModel && $type === self::TYPE_DOCTRINE)
        {
            return new DoctrineModel($table);
        }

        if ($isModel && $type === self::TYPE_WILDFIRE)
        {
            return new WildfireModel($table);
        }

        return new Controller($table, $type);
    }
}
