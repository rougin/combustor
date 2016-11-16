<?php

namespace Rougin\Combustor\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Rougin\Combustor\Common\DataGenerator;

/**
 * Make View Command
 *
 * @package Combustor
 * @author  Rougin Royce Gutib <rougingutib@gmail.com>
 */
class MakeViewCommand extends AbstractCommand
{
    /**
     * Set the configurations of the specified command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('make:view')->setDescription('Create a new view folder');
        $this->addArgument('table', InputArgument::REQUIRED, 'Name of the table');
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
        $contents = (new DataGenerator($this->describe, $input))->generate();
        $filename = strtolower(plural(underscore($input->getArgument('table'))));

        $views = [ 'create', 'edit', 'index', 'show' ];

        foreach ($views as $item) {
            $file = 'application/views/' . $filename . '/' . $item . '.php';
            $view = $this->renderer->render('Views/' . ucfirst($item) . '.twig', $contents);

            $this->filesystem->write($file, $view);
        }

        $output->writeln('<info>Views created successfully!</info>');
    }
}
