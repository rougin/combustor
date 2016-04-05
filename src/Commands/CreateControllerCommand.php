<?php

namespace Rougin\Combustor\Commands;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Rougin\Combustor\Common\Tools;
use Rougin\Combustor\Common\Inflector;
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
     * Override this to check for x or y and return false if the command can not
     * run properly under the current conditions.
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
                'Name of the controller'
            )->addOption(
                'camel',
                NULL,
                InputOption::VALUE_NONE,
                'Uses the camel case naming convention'
            )->addOption(
                'doctrine',
                NULL,
                InputOption::VALUE_NONE,
                'Generates a controller based on Doctrine'
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
            )->addOption(
                'wildfire',
                NULL,
                InputOption::VALUE_NONE,
                'Generates a controller based on Wildfire'
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
        $fileName = ucfirst($input->getArgument('name'));

        if ($input->getOption('keep')) {
            $fileName = ucfirst(Inflector::plural($input->getArgument('name')));
        }

        $path = APPPATH . 'controllers' . DIRECTORY_SEPARATOR . $fileName . '.php';

        $fileInformation = [
            'name' => $fileName,
            'type' => 'controller',
            'path' => $path
        ];

        $validator = new ControllerValidator(
            $input->getOption('doctrine'),
            $input->getOption('wildfire'),
            $input->getOption('camel'),
            $fileInformation
        );

        if ($validator->fails()) {
            $message = $validator->getMessage();

            return $output->writeln('<error>' . $message . '</error>');
        }

        $data = [
            'file' => $fileInformation,
            'isCamel' => $input->getOption('camel'),
            'name' => $input->getArgument('name'),
            'title' => strtolower($fileName),
            'type' => $validator->getLibrary()
        ];

        $generator = new ControllerGenerator($this->describe, $data);

        $result = $generator->generate();
        $controller = $this->renderer->render('Controller.template', $result);
        $message = 'The controller "' . $fileName . '" has been created successfully!';

        $file = fopen($path, 'wb');
        file_put_contents($path, $controller);
        fclose($file);

        return $output->writeln('<info>' . $message . '</info>');
    }
}
