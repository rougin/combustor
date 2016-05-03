<?php

namespace Rougin\Combustor\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Rougin\Combustor\Common\File;
use Rougin\Combustor\Common\Tools;
use Rougin\Combustor\Common\Commands\InstallCommand;

/**
 * Install Wildfire Command
 *
 * Installs Wildfire for CodeIgniter
 * 
 * @package Combustor
 * @author  Rougin Royce Gutib <rougingutib@gmail.com>
 */
class InstallWildfireCommand extends InstallCommand
{
    /**
     * @var string
     */
    protected $library = 'wildfire';

    /**
     * Checks whether the command is enabled or not in the current environment.
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return ! file_exists(APPPATH . 'libraries/Doctrine.php');
    }

    /**
     * Executes the command.
     * 
     * @param  \Symfony\Component\Console\Input\InputInterface   $input
     * @param  \Symfony\Component\Console\Output\OutputInterface $output
     * @return object|\Symfony\Component\Console\Output\OutputInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->addLibrary('wildfire');

        $template = $this->renderer->render('Libraries/Wildfire.template');
        $wildfire = new File(APPPATH . 'libraries/Wildfire.php');

        $wildfire->putContents($template);
        $wildfire->close();

        Tools::ignite();

        $message = 'Wildfire is now installed successfully!';

        return $output->writeln('<info>' . $message . '</info>');
    }
}
