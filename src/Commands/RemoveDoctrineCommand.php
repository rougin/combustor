<?php

namespace Rougin\Combustor\Commands;

use Rougin\Combustor\Common\Tools;
use Rougin\Combustor\Common\AbstractCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
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
     * @return OutputInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $message = Tools::removeLibrary('doctrine');

        return $output->writeln('<info>'.$message.'</info>');
    }
}
