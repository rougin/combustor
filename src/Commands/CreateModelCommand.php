<?php

namespace Rougin\Combustor\Commands;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Rougin\Combustor\Common\Tools;
use Rougin\Combustor\Validator\ModelValidator;
use Rougin\Combustor\Generator\ModelGenerator;

/**
 * Create Model Command
 *
 * Generates a Wildfire or Doctrine-based model for CodeIgniter
 * 
 * @package Combustor
 * @author  Rougin Royce Gutib <rougingutib@gmail.com>
 */
class CreateModelCommand extends AbstractCommand
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
            ->setName('create:model')
            ->setDescription('Creates a new model')
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
                'doctrine',
                NULL,
                InputOption::VALUE_NONE,
                'Generates a model based on Doctrine'
            )->addOption(
                'lowercase',
                NULL,
                InputOption::VALUE_NONE,
                'Keeps the first character of the name to lowercase'
            )->addOption(
                'wildfire',
                NULL,
                InputOption::VALUE_NONE,
                'Generates a model based on Wildfire'
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
        $fileName = ucfirst(singular($input->getArgument('name')));

        $path = APPPATH . 'models' . DIRECTORY_SEPARATOR . $fileName . '.php';

        $fileInformation = [
            'name' => $fileName,
            'type' => 'model',
            'path' => $path
        ];

        $validator = new ModelValidator(
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
            'type' => $validator->getLibrary()
        ];

        $generator = new ModelGenerator($this->describe, $data);

        $result = $generator->generate();
        $model = $this->renderer->render('Model.template', $result);
        $message = 'The model "' . $fileName . '" has been created successfully!';

        $file = fopen($path, 'wb');
        file_put_contents($path, $model);
        fclose($file);

        return $output->writeln('<info>' . $message . '</info>');
    }
}
