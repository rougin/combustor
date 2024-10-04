<?php

namespace Rougin\Combustor\Commands;

use Rougin\Blueprint\Command;

/**
 * @package Combustor
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class InstallDoctrine extends Command
{
    /**
     * @var string
     */
    protected $name = 'install:doctrine';

    /**
     * @var string
     */
    protected $description = 'Install the Doctrine package';

    /**
     * Checks whether the command is enabled or not in the current environment.
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return ! class_exists('Rougin\Credo\Credo');
    }

    /**
     * Executes the command.
     *
     * @return integer
     */
    public function run()
    {
        system('composer require rougin/credo');

        $this->showPass('Doctrine installed successfully!');

        return self::RETURN_SUCCESS;
    }
}
