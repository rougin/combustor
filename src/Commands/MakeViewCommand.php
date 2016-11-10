<?php

namespace Rougin\Combustor\Commands;

use Symfony\Component\Console\Input\InputOption;
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
     * @var string
     */
    protected $command = 'view';

    /**
     * Set the configurations of the specified command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('make:view')->setDescription('Creates a new view');
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
        $directory = strtolower(plural($filename));

        $create = $this->renderer->render('Views/Create.twig', $contents);
        $edit   = $this->renderer->render('Views/Edit.twig', $contents);
        $index  = $this->renderer->render('Views/Index.twig', $contents);
        $show   = $this->renderer->render('Views/Show.twig', $contents);

        $this->filesystem->write($directory . '/create.php', $create);
        $this->filesystem->write($directory . '/edit.php', $edit);
        $this->filesystem->write($directory . '/index.php', $index);
        $this->filesystem->write($directory . '/show.php', $show);

        $output->writeln('<info>Views created successfully!</info>');
    }
}
