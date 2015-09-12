<?php

namespace Rougin\Combustor\Commands;

use Rougin\Blueprint\AbstractCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Remove Doctrine Command
 *
 * Removes Doctrine from CodeIgniter
 * 
 * @package Combustor
 * @author  Rougin Royce Gutib <rougingutib@gmail.com>
 */
class RemoveDoctrineCommand extends AbstractCommand
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
        if (file_exists(APPPATH . 'libraries/Doctrine.php')) {
            return TRUE;
        }

        return FALSE;
    }

    /**
     * Sets the configurations of the specified command.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('remove:doctrine')
            ->setDescription('Remove the Doctrine ORM');
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
         * ---------------------------------------------------------------------------------------------
         * Adding the Doctrine.php to the "libraries" directory
         * ---------------------------------------------------------------------------------------------
         */

        if ( ! file_exists(APPPATH . 'libraries/Doctrine.php')) {
            exit($output->writeln('<error>The Doctrine ORM is not installed!</error>'));
        }

        $autoload = file_get_contents(APPPATH . 'config/autoload.php');

        preg_match_all('/\$autoload\[\'libraries\'\] = array\((.*?)\)/', $autoload, $match);

        $libraries = explode(', ', end($match[1]));

        if (in_array('\'doctrine\'', $libraries)) {
            $position = array_search('\'doctrine\'', $libraries);

            unset($libraries[$position]);

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

        $composer = file_get_contents('composer.json');
        $composer = str_replace(array(' ', "\n", "\r") , array('', '', ''), $composer);

        preg_match_all('/"require": \{(.*?)\}/', $composer, $match);
        $requiredLibraries = explode(',', end($match[1]));

        preg_match_all('/"require-dev": \{(.*?)\}/', $composer, $match);
        $requiredDevLibraries = explode(',', end($match[1]));

        if (in_array('"doctrine/orm": "2.4.*"', $requiredLibraries)) {
            $position = array_search('"doctrine/orm": "2.4.*"', $requiredLibraries);

            unset($requiredLibraries[$position]);

            $composer =
'{
    "description" : "The CodeIgniter framework",
    "name" : "codeigniter/framework",
    "license": "MIT",
    "require": {' . "\n    " . implode(',' . "\n    ", $requiredLibraries) . "\n  " . '},
    "require-dev": {' . "\n    " . implode(',' . "\n    ", $requiredDevLibraries) . "\n  " . '}
}';

            $file = fopen('composer.json', 'wb');

            file_put_contents('composer.json', $composer);
            fclose($file);
        }

        system('composer update');

        if (unlink(APPPATH . 'libraries/Doctrine.php')) {
            $output->writeln('<info>The Doctrine ORM is now successfully removed!</info>');
        } else {
            $output->writeln('<error>There\'s something wrong while removing. Please try again later.</error>');
        }
    }
}
