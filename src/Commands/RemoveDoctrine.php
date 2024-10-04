<?php

namespace Rougin\Combustor\Commands;

use Rougin\Blueprint\Command;
use Rougin\Combustor\Combustor;

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
     * @var string
     */
    protected $path = '';

    /**
     * @param \Rougin\Combustor\Combustor $combustor
     */
    public function __construct(Combustor $combustor)
    {
        $this->path = $combustor->getAppPath();
    }

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

        $file = $this->path . '/core/Loader.php';

        if (file_exists($file))
        {
            unlink($file);
        }

        $this->showPass('Doctrine removed successfully!');

        return self::RETURN_SUCCESS;
    }
}
