<?php

namespace Rougin\Combustor\Commands;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Rougin\Combustor\Common\File;
use Rougin\Combustor\Common\Tools;
use Rougin\Combustor\Validator\ControllerValidator;
use Rougin\Combustor\Generator\ControllerGenerator;

/**
 * Create Controller Command
 *
 * Generates a Wildfire or Doctrine-based controller for CodeIgniter.
 * 
 * @package Combustor
 * @author  Rougin Royce Gutib <rougingutib@gmail.com>
 */
class CreateControllerCommand extends AbstractCommand
{
    /**
     * Checks whether the command is enabled or not in the current environment.
     *
     * @return bool
     */
    public function isEnabled()
    {
        return Tools::isCommandEnabled();
    }

    /**
     * Sets the configurations of the specified command.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('create:controller')
            ->setDescription('Creates a new controller')
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'Name of the table'
            )->addOption(
                'camel',
                NULL,
                InputOption::VALUE_NONE,
                'Uses the camel case naming convention'
            )->addOption(
                'keep',
                NULL,
                InputOption::VALUE_NONE,
                'Keeps the name to be used'
            )->addOption(
                'lowercase',
                NULL,
                InputOption::VALUE_NONE,
                'Keeps the first character of the name to lowercase'
            );
    }

    /**
     * Executes the command.
     * 
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return object|\Symfony\Component\Console\Output\OutputInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fileName = ucfirst(plural($input->getArgument('name')));

        if ($input->getOption('keep')) {
            $fileName = ucfirst($input->getArgument('name'));
        }

        $path = APPPATH . 'controllers' . DIRECTORY_SEPARATOR . $fileName . '.php';

        $info = [
            'name' => $fileName,
            'type' => 'controller',
            'path' => $path
        ];

        $validator = new ControllerValidator($input->getOption('camel'), $info);

        if ($validator->fails()) {
            $message = $validator->getMessage();

            return $output->writeln('<error>' . $message . '</error>');
        }

        $data = [
            'file' => $info,
            'isCamel' => $input->getOption('camel'),
            'name' => $input->getArgument('name'),
            'title' => strtolower($fileName),
            'type' => $validator->getLibrary()
        ];

        $generator = new ControllerGenerator($this->describe, $data);

        $result = $generator->generate();
        $controller = $this->renderer->render('Controller.template', $result);
        $message = 'The controller "' . $fileName . '" has been created successfully!';

        $file = new File($path);

        $file->putContents($controller);
        $file->close();

        return $output->writeln('<info>' . $message . '</info>');
    }
}
