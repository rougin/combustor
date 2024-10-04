<?php

namespace Rougin\Combustor\Commands;

use Rougin\Blueprint\Command;
use Rougin\Classidy\Classidy;
use Rougin\Classidy\Generator;
use Rougin\Combustor\Combustor;

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

        $class = new Classidy;

        $class->setName('MY_Loader');
        $class->extendsTo('Rougin\Credo\Loader');

        $maker = new Generator;

        $result = $maker->make($class);

        $file = $this->path . '/core/Loader.php';

        if (! file_exists($file))
        {
            file_put_contents($file, $result);
        }

        $this->showPass('Doctrine installed successfully!');

        return self::RETURN_SUCCESS;
    }
}
