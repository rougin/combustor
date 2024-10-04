<?php

namespace Rougin\Combustor;

use Rougin\Blueprint\Command as Blueprint;
use Rougin\Classidy\Generator;
use Rougin\Combustor\Template\Controller;
use Rougin\Combustor\Template\Doctrine\Model as DoctrineModel;
use Rougin\Combustor\Template\Wildfire\Model as WildfireModel;

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
     * @var \Rougin\Describe\Driver\DriverInterface|null
     */
    protected $driver = null;

    /**
     * @var string[]
     */
    protected $excluded = array();

    /**
     * @var \Rougin\Classidy\Generator
     */
    protected $maker;

    /**
     * @var string
     */
    protected $path;

    /**
     * @param \Rougin\Combustor\Combustor $combustor
     * @param \Rougin\Classidy\Generator  $maker
     */
    public function __construct(Combustor $combustor, Generator $maker)
    {
        $this->driver = $combustor->getDriver();

        $this->excluded = $combustor->getExcluded();

        $this->maker = $maker;

        $this->path = $combustor->getAppPath();
    }

    /**
     * @return void
     */
    public function init()
    {
        $this->addArgument('table', 'Name of the database table');

        if ($this->name !== 'create:views')
        {
            $this->addOption('keep', 'Do not inflect the class name');
        }
    }

    /**
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->driver !== null;
    }

    /**
     * @param boolean $doctrine
     * @param boolean $wildfire
     * @return integer
     * @throws \Exception
     */
    public static function getInstalled($doctrine, $wildfire)
    {
        $class = 'Rougin\Credo\Credo';
        $doctrineExists = class_exists($class);

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

        if (! $wildfire && ($doctrine || $doctrineExists))
        {
            return self::TYPE_DOCTRINE;
        }

        return self::TYPE_WILDFIRE;
    }

    /**
     * @param integer $type
     *
     * @return \Rougin\Classidy\Classidy
     */
    protected function getTemplate($type)
    {
        /** @var string */
        $table = $this->getArgument('table');

        $isModel = $this->name === 'create:model';

        /** @var \Rougin\Describe\Driver\DriverInterface */
        $driver = $this->driver;

        $cols = $driver->columns($table);

        if ($isModel && $type === self::TYPE_DOCTRINE)
        {
            return new DoctrineModel($table, $cols, $this->excluded);
        }

        if ($isModel && $type === self::TYPE_WILDFIRE)
        {
            return new WildfireModel($table, $cols, $this->excluded);
        }

        return new Controller($table, $type);
    }
}
