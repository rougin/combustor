<?php

namespace Rougin\Combustor\Commands;

use Rougin\Combustor\Command;

/**
 * @package Combustor
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class Initialize extends Command
{
    /**
     * @var string
     */
    protected $file = 'combustor.yml';

    /**
     * @var string
     */
    protected $name = 'init';

    /**
     * Configures the current command.
     *
     * @return void
     */
    public function init()
    {
        $text = 'Creates a "' . $this->file . '" file';

        $this->description = $text;

        $text = 'Allows to create a "combustor.yml" file in the current working directory.';

        $this->help = (string) $text;
    }

    /**
     * Executes the command.
     *
     * @return integer
     */
    public function run()
    {
        /** @var string */
        $path = realpath(__DIR__ . '/../Template');

        /** @var string */
        $file = file_get_contents($path . '/' . $this->file);

        $root = $this->getRootPath();

        file_put_contents($root . '/' . $this->file, $file);

        $text = '"' . $this->file . '" added successfully!';

        $this->showPass($text);

        return Command::RETURN_SUCCESS;
    }

    /**
     * Checks whether the command is enabled or not in the current environment.
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return ! file_exists($this->getRootPath() . '/' . $this->file);
    }

    /**
     * Returns the root directory from the package.
     *
     * @return string
     */
    protected function getRootPath()
    {
        /** @var string */
        $vendor = realpath(__DIR__ . '/../../../../../');

        $exists = file_exists($vendor . '/../autoload.php');

        return $exists ? $vendor : __DIR__ . '/../../';
    }
}
