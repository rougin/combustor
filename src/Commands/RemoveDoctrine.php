<?php

namespace Rougin\Combustor\Commands;

use Rougin\Blueprint\Command;

/**
 * @package Combustor
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class RemoveDoctrine extends Command
{
    /**
     * @var string
     */
    protected $name = 'remove:doctrine';

    /**
     * @var string
     */
    protected $description = 'Remove the Doctrine package';

    /**
     * Checks whether the command is enabled or not in the current environment.
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return class_exists('Rougin\Credo\Credo');
    }

    /**
     * Executes the command.
     *
     * @return integer
     */
    public function run()
    {
        system('composer remove rougin/credo');

        $this->showPass('Doctrine removed successfully!');

        return self::RETURN_SUCCESS;
    }
}
