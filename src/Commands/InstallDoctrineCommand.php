<?php

namespace Rougin\Combustor\Commands;

use Rougin\Blueprint\AbstractCommand;
use Rougin\Combustor\Tools;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Install Doctrine Command
 *
 * Installs Doctrine for CodeIgniter
 * 
 * @package Combustor
 * @author  Rougin Royce Gutib <rougingutib@gmail.com>
 */
class InstallDoctrineCommand extends AbstractCommand
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
            ->setName('install:doctrine')
            ->setDescription('Installs the Doctrine ORM');
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
        system('composer require doctrine/orm');

        $cli = $this->renderer->render('DoctrineCLI.template');
        $library = $this->renderer->render('Libraries/Doctrine.template');

        /**
         * Modify the contents of vendor/bin/doctrine.php,
         * create the Doctrine library and create a "proxies"
         * directory for lazy loading
         */

        file_put_contents(VENDOR . 'bin/doctrine.php', $cli);
        file_put_contents(VENDOR . 'doctrine/orm/bin/doctrine.php', $cli);

        $file = fopen(APPPATH . 'libraries/Doctrine.php', 'wb');
        file_put_contents(APPPATH . 'libraries/Doctrine.php', $library);
        fclose($file);

        $autoload = file_get_contents(APPPATH . 'config/autoload.php');

        preg_match_all(
            '/\$autoload\[\'libraries\'\] = array\((.*?)\)/',
            $autoload,
            $match
        );

        $libraries = explode(', ', end($match[1]));

        if ( ! in_array('\'doctrine\'', $libraries)) {
            array_push($libraries, '\'doctrine\'');

            if (in_array('\'database\'', $libraries)) {
                $position = array_search('\'database\'', $libraries);

                unset($libraries[$position]);
            }

            $libraries = array_filter($libraries);

            $autoload = preg_replace(
                '/\$autoload\[\'libraries\'\] = array\([^)]*\);/',
                '$autoload[\'libraries\'] = array(' .
                    implode(', ', $libraries) .
                    ');',
                $autoload
            );

            $file = fopen(APPPATH . 'config/autoload.php', 'wb');

            file_put_contents(APPPATH . 'config/autoload.php', $autoload);
            fclose($file);
        }

        if ( ! is_dir(APPPATH . 'models/proxies')) {
            mkdir(APPPATH . 'models/proxies');
            chmod(APPPATH . 'models/proxies', 0777);
        }

        /*
         * Include the Base Model class in Doctrine CLI
         */

        $abstractCommand = file_get_contents(
            VENDOR .
            'doctrine/orm/lib/Doctrine/' .
            'ORM/Tools/Console/Command/' .
            'SchemaTool/AbstractCommand.php'
        );

        $search  = 'use Doctrine\ORM\Tools\SchemaTool;';
        $replace = $search . "\n" . 'include BASEPATH . \'core/Model.php\';';

        $contents = $abstractCommand;
        $schemaTool = 'use Doctrine\ORM\Tools\SchemaTool;';
        $coreModel = 'include BASEPATH . \'core/Model.php\';';

        if (strpos($abstractCommand, $schemaTool) !== FALSE) {
            if (strpos($abstractCommand, $coreModel) === FALSE) {
                $contents = str_replace($search, $replace, $abstractCommand);
            }
        }

        file_put_contents(
            VENDOR .
            'doctrine/orm/lib/Doctrine/' .
            'ORM/Tools/Console/Command/' .
            'SchemaTool/AbstractCommand.php',
            $contents
        );

        Tools::ignite();

        $message = 'Doctrine ORM is now installed successfully!';

        return $output->writeln('<info>' . $message . '</info>');
    }
}
