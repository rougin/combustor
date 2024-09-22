<?php

namespace Rougin\Combustor;

use Rougin\Blueprint\Command as Blueprint;
use Rougin\Classidy\Generator;
use Rougin\Combustor\Template\Controller;
use Rougin\Describe\Driver\DriverInterface;

/**
 * @package Combustor
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class Command extends Blueprint
{
    /**
     * @var \Rougin\Describe\Driver\DriverInterface
     */
    protected $driver;

    /**
     * @var \Rougin\Classidy\Generator
     */
    protected $maker;

    /**
     * @param \Rougin\Describe\Driver\DriverInterface $driver
     */
    public function __construct(DriverInterface $driver, Generator $maker)
    {
        $this->driver = $driver;

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
        /** @var string */
        $table = $this->getArgument('name');

        /** @var boolean */
        $lower = $this->getOption('lowercase');
        $lower = (bool) $lower;

        $plate = $this->getTemplate($table, $lower);

        $plate = $this->maker->make($plate);

        $name = ucfirst(Inflector::plural($table));
        $file = APPPATH . 'controllers/' . $name . '.php';
        file_put_contents($file, $plate);

        $this->showPass('Controller successfully created!');

        return self::RETURN_SUCCESS;
    }

    /**
     * @param string  $table
     * @param boolean $lower
     *
     * @return \Rougin\Classidy\Classidy
     */
    protected function getTemplate($table, $lower)
    {
        return new Controller($table, $lower);
    }
}
