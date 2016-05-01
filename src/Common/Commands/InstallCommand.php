<?php

namespace Rougin\Combustor\Common\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Rougin\Combustor\Common\Tools;
use Rougin\Combustor\Common\Config;
use Rougin\Combustor\Commands\AbstractCommand;

/**
 * Install Command
 *
 * Installs Doctrine/Wildfire library for CodeIgniter.
 * 
 * @package Combustor
 * @author  Rougin Royce Gutib <rougingutib@gmail.com>
 */
class InstallCommand extends AbstractCommand
{
    /**
     * @var string
     */
    protected $library = '';

    /**
     * Checks whether the command is enabled or not in the current environment.
     *
     * Override this to check for x or y and return false if the command can not
     * run properly under the current conditions.
     *
     * @return boolean
     */
    public function isEnabled()
    {
        $library = ucfirst($this->library);

        return ! file_exists(APPPATH . 'libraries/' . $library . '.php');
    }

    /**
     * Sets the configurations of the specified command.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('install:' . $this->library)
            ->setDescription('Installs ' . ucfirst($this->library));
    }

    /**
     * Adds the specified library in the autoload.php.
     * 
     * @param  string $library
     * @return void
     */
    protected function addLibrary($library)
    {
        $autoload = new Config('autoload', APPPATH . 'config');

        $libraries = $autoload->get('libraries', 60, 'array');

        if ( ! in_array($library, $libraries)) {
            array_push($libraries, $library);

            $autoload->set('libraries', 60, $libraries, 'array');
            $autoload->save();
        }
    }
}
