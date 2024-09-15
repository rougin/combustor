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
     * @var \Rougin\Describe\Driver\DriverInterface
     */
    protected $driver;

    /**
     * @param \Rougin\Describe\Driver\DriverInterface $driver
     */
    public function __construct(DriverInterface $driver)
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
}
