<?php

namespace Rougin\Combustor\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Rougin\Combustor\Common\DataGenerator;
use Rougin\Combustor\Exceptions\ModelNotFoundException;

/**
 * Make Model Command
 *
 * @package Combustor
 * @author  Rougin Royce Gutib <rougingutib@gmail.com>
 */
class MakeModelCommand extends AbstractCommand
{
    /**
     * Set the configurations of the specified command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('make:model')->setDescription('Create a new model class');
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
        load_class('Model', 'core');

        if (class_exists('Rougin\Wildfire\CodeigniterModel')) {
            $modelType = 'Models/Wildfire';
        } elseif (class_exists('Rougin\Credo\CodeigniterModel')) {
            $modelType = 'Models/Credo';
        } else {
            throw new ModelNotFoundException('Credo or Wildfire is not yet installed!');
        }

        $contents  = (new DataGenerator($this->describe, $input))->generate();
        $filename  = ucfirst(singular(underscore($input->getArgument('table'))));
        $rendered  = $this->renderer->render($modelType . '.twig', $contents);

        $this->filesystem->write('application/models/' . $filename . '.php', $rendered);

        $output->writeln('<info>Model created successfully!</info>');
    }
}
