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
        return Tools::isDoctrineEnabled();
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
            ->setDescription('Removes Doctrine ORM');
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
        $autoload = file_get_contents(APPPATH . 'config/autoload.php');

        preg_match_all(
            '/\$autoload\[\'libraries\'\] = array\((.*?)\)/',
            $autoload,
            $match
        );

        $libraries = explode(', ', end($match[1]));

        if (in_array('\'doctrine\'', $libraries)) {
            $position = array_search('\'doctrine\'', $libraries);

            unset($libraries[$position]);

            $libraries = array_filter($libraries);

            $autoload = preg_replace(
                '/\$autoload\[\'libraries\'\] = array\([^)]*\);/',
                '$autoload[\'libraries\'] = array(' .
                    implode(', ', $libraries) . ');',
                $autoload
            );

            $file = fopen(APPPATH . 'config/autoload.php', 'wb');

            file_put_contents(APPPATH . 'config/autoload.php', $autoload);
            fclose($file);
        }

        system('composer remove doctrine/orm');

        if ( ! unlink(APPPATH . 'libraries/Doctrine.php')) {
            $message = 'There\'s something wrong while removing. ' .
                'Please try again later.';

            return $output->writeln('<error>' . $message . '</error>');
        }

        $message = 'Doctrine ORM is now successfully removed!';

        return $output->writeln('<info>' . $message . '</info>');
    }
}
