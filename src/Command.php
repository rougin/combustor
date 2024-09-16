<?php

namespace Rougin\Combustor;

use Rougin\Blueprint\Command as Blueprint;
use Rougin\Describe\Driver\DriverInterface;

/**
 * @package Combustor
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class Command extends Blueprint
{
    /**
     * @var \Rougin\Describe\Driver\DriverInterface|null
     */
    protected $driver;

    /**
     * @param \Rougin\Describe\Driver\DriverInterface|null $driver
     */
    public function __construct(DriverInterface $driver = null)
    {
        $this->driver = $driver;
    }

    /**
     * Configures the current command.
     *
     * @return void
     */
    public function init()
    {
        $this->addArgument('name', 'Name of the table');

        if ($this->name === 'create:layout')
        {
            $this->addOption('bootstrap', 'Includes Bootstrap styling');
        }

        $this->addOption('camel', 'Uses the camelCase convention');

        $this->addOption('keep', 'Keeps the name to be used');

        if ($this->name === 'create:controller')
        {
            $this->addOption('lowercase', 'Keeps the first character of the name to lowercase');
        }
    }

    /**
     * Checks whether the command is enabled or not in the current environment.
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->driver !== null;
    }

    /**
     * Executes the command.
     *
     * @return integer
     */
    public function run()
    {
        return self::RETURN_SUCCESS;
    }
}
