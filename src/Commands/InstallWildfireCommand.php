<?php

namespace Rougin\Combustor\Commands;

use Rougin\Blueprint\AbstractCommand;
use Rougin\Combustor\Tools;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Install Wildfire Command
 *
 * Installs Wildfire for CodeIgniter
 * 
 * @package Combustor
 * @author  Rougin Royce Gutib <rougingutib@gmail.com>
 */
class InstallWildfireCommand extends AbstractCommand
{
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
        if (file_exists(APPPATH . 'libraries/Wildfire.php')) {
            return FALSE;
        }

        return TRUE;
    }

    /**
     * Sets the configurations of the specified command.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('install:wildfire')
            ->setDescription('Install Wildfire');
    }

    /**
     * Executes the command.
     * 
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     * @return object|OutputInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /**
         * Add Wildfire.php to the "libraries" directory
         */

        $autoload = file_get_contents(APPPATH . 'config/autoload.php');

        preg_match_all('/\$autoload\[\'libraries\'\] = array\((.*?)\)/', $autoload, $match);

        $libraries = explode(', ', end($match[1]));

        if ( ! in_array('\'wildfire\'', $libraries)) {
            array_push($libraries, '\'wildfire\'');

            $libraries = array_filter($libraries);

            $autoload = preg_replace(
                '/\$autoload\[\'libraries\'\] = array\([^)]*\);/',
                '$autoload[\'libraries\'] = array(' . implode(', ', $libraries) . ');',
                $autoload
            );

            $file = fopen(APPPATH . 'config/autoload.php', 'wb');

            file_put_contents(APPPATH . 'config/autoload.php', $autoload);
            fclose($file);
        }

        $file = fopen(APPPATH . 'libraries/Wildfire.php', 'wb');
        $wildfire = $this->renderer->render('Libraries/Wildfire.php');

        file_put_contents(APPPATH . 'libraries/Wildfire.php', $wildfire);
        fclose($file);

        Tools::ignite();
        $output->writeln('<info>Wildfire is now installed successfully!</info>');
    }
}
