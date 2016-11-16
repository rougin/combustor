<?php

namespace Rougin\Combustor\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Make Layout Command
 *
 * @package Combustor
 * @author  Rougin Royce Gutib <rougingutib@gmail.com>
 */
class MakeLayoutCommand extends AbstractCommand
{
    /**
     * Set the configurations of the specified command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('make:layout')->setDescription('Create a new view layout');
    }

    /**
     * Executes the current command.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @param  \Symfony\Component\Console\Input\OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $header = $this->renderer->render('Views/Layout/Header.twig');
        $footer = $this->renderer->render('Views/Layout/Footer.twig');

        $this->filesystem->write('application/views/layout/header.php', $header);
        $this->filesystem->write('application/views/layout/footer.php', $footer);

        $output->writeln('<info>Layout created successfully!</info>');
    }
}
