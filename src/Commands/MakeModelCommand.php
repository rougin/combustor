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
     * @var string
     */
    protected $command = 'model';

    /**
     * Set the configurations of the specified command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('make:model')->setDescription('Creates a new model');
        $this->addArgument('table', InputArgument::REQUIRED, 'Name of the table');

        // $this->addOption('camel', null, InputOption::VALUE_NONE, 'Uses the camel case naming convention');
        // $this->addOption('keep', null, InputOption::VALUE_NONE, 'Keeps the name to be used');
        // $this->addOption('overwrite', null, InputOption::VALUE_NONE, 'Overwrite the specified file');
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
        $contents  = (new DataGenerator($this->describe, $input))->generate();
        $converter = $this->renderer->getExtension('CaseExtension');
        $filename  = $converter->toUnderscoreCase($input->getArgument('table'));
        $rendered  = $this->renderer->render($this->getModelType() . '.twig', $contents);

        $this->filesystem->write(ucfirst(singular($filename)) . '.php', $rendered);

        $output->writeln('<info>Model created successfully!</info>');
    }

    /**
     * Returns the existing type to be used as the model template.
     *
     * @return string
     */
    protected function getModelType()
    {
        load_class('Model', 'core');

        if (class_exists('Rougin\Wildfire\CodeigniterModel')) {
            return 'Models/Wildfire';
        }

        if (class_exists('Rougin\Credo\CodeigniterModel')) {
            return 'Models/Credo';
        }

        throw new ModelNotFoundException('Credo or Wildfire is not yet installed!');
    }
}
