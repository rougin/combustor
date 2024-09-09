<?php

namespace Rougin\Combustor\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Rougin\Combustor\Common\File;
use Rougin\Combustor\Common\Tools;
use Rougin\Combustor\Common\Commands\InstallCommand;

/**
 * Install Doctrine Command
 *
 * Installs Doctrine for CodeIgniter
 *
 * @package Combustor
 * @author  Rougin Gutib <rougingutib@gmail.com>
 */
class InstallDoctrineCommand extends InstallCommand
{
    /**
     * @var string
     */
    protected $library = 'doctrine';

    /**
     * Executes the command.
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return object|\Symfony\Component\Console\Output\OutputInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        system('composer require doctrine/orm:"~1.0|~2.0"');

        $cli = $this->renderer->render('DoctrineCLI.tpl');
        $doctrine = new File(APPPATH . 'libraries/Doctrine.php');
        $template = $this->renderer->render('Libraries/Doctrine.tpl');
        $vendor = realpath('vendor');

        // Modifies the contents of vendor/bin/doctrine.php, creates the
        // Doctrine library and creates a "proxies" directory for lazy loading.
        file_put_contents($vendor . '/bin/doctrine.php', $cli);
        file_put_contents($vendor . '/doctrine/orm/bin/doctrine.php', $cli);

        $doctrine->putContents($template);
        $doctrine->close();

        $this->addLibrary('doctrine');

        if (! is_dir(APPPATH . 'models/proxies')) {
            mkdir(APPPATH . 'models/proxies');
            chmod(APPPATH . 'models/proxies', 0777);
        }

        $abstractCommandPath = $vendor . '/doctrine/orm/lib/Doctrine/ORM/' .
            'Tools/Console/Command/SchemaTool/AbstractCommand.php';

        // Includes the Base Model class in Doctrine CLI
        $abstractCommand = file_get_contents($abstractCommandPath);

        $search = 'use Doctrine\ORM\Tools\SchemaTool;';
        $replace = $search . "\n" . 'include BASEPATH . \'core/Model.php\';';

        $contents = $abstractCommand;
        $schemaTool = 'use Doctrine\ORM\Tools\SchemaTool;';
        $coreModel = 'include BASEPATH . \'core/Model.php\';';

        if (strpos($abstractCommand, $schemaTool) !== false) {
            if (strpos($abstractCommand, $coreModel) === false) {
                $contents = str_replace($search, $replace, $abstractCommand);
            }
        }

        file_put_contents($abstractCommandPath, $contents);

        Tools::ignite();

        $message = 'Doctrine ORM is now installed successfully!';

        return $output->writeln('<info>' . $message . '</info>');
    }
}
