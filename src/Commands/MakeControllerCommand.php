<?php

namespace Rougin\Combustor\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Rougin\Combustor\Common\DataGenerator;
use Rougin\Combustor\Exceptions\ModelNotFoundException;

/**
 * Make Controller Command
 *
 * @package Combustor
 * @author  Rougin Royce Gutib <rougingutib@gmail.com>
 */
class MakeControllerCommand extends AbstractCommand
{
    /**
     * Set the configurations of the specified command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('make:controller')->setDescription('Create a new controller class');
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
        $contents  = (new DataGenerator($this->describe, $input))->generate();
        $converter = $this->renderer->getExtension('CaseExtension');
        $filename  = $converter->toUnderscoreCase($input->getArgument('table'));
        $rendered  = $this->renderer->render('Controller.twig', $contents);

        $this->filesystem->write('application/controllers/' . ucfirst(plural($filename)) . '.php', $rendered);

        $output->writeln('<info>Controller created successfully!</info>');
    }
}
