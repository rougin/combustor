<?php

namespace Rougin\Combustor\Common\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Rougin\Combustor\Common\Tools;
use Rougin\Combustor\Common\Config;
use Rougin\Combustor\Commands\AbstractCommand;

/**
 * Remove Command
 *
 * Removes Doctrine/Wildfire library from CodeIgniter.
 *
 * @package Combustor
 * @author  Rougin Royce Gutib <rougingutib@gmail.com>
 */
class RemoveCommand extends AbstractCommand
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
     * @return bool
     */
    public function isEnabled()
    {
        $file = APPPATH . 'libraries/' . $this->library . '.php';
        
        return file_exists($file);
    }

    /**
     * Sets the configurations of the specified command.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('remove:' . $this->library)
            ->setDescription('Removes ' . ucfirst($this->library));
    }

    /**
     * Executes the command.
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return OutputInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $autoload = new Config('autoload', APPPATH . 'config');

        $libraries = $autoload->get('libraries', 60, 'array');

        if (in_array($this->library, $libraries)) {
            $position = array_search($this->library, $libraries);

            unset($libraries[$position]);

            $autoload->set('libraries', 60, $libraries, 'array');
            $autoload->save();
        }

        if ($this->library == 'doctrine') {
            system('composer remove doctrine/orm');
        }

        unlink(APPPATH . 'libraries/' . ucfirst($this->library) . '.php');

        $message = ucfirst($this->library) . ' is now successfully removed!';

        return $output->writeln('<info>' . $message . '</info>');
    }
}
