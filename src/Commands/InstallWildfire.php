<?php

namespace Rougin\Combustor\Commands;

use Rougin\Blueprint\Command;

/**
 * @package Combustor
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class InstallWildfire extends Command
{
    /**
     * @var string
     */
    protected $name = 'install:wildfire';

    /**
     * @var string
     */
    protected $description = 'Install the Wildfire package';

    /**
     * Checks whether the command is enabled or not in the current environment.
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return ! class_exists('Rougin\Wildfire\Wildfire');
    }

    /**
     * Executes the command.
     *
     * @return integer
     */
    public function run()
    {
        system('composer require rougin/wildfire');

        $this->showPass('Wildfire installed successfully!');

        return self::RETURN_SUCCESS;
    }
}
