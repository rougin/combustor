<?php

namespace Rougin\Combustor\Commands;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Rougin\Combustor\Common\File;
use Rougin\Combustor\Common\Tools;
use Rougin\Combustor\Validator\ModelValidator;
use Rougin\Combustor\Generator\ModelGenerator;

/**
 * Create Model Command
 *
 * Generates a Wildfire or Doctrine-based model for CodeIgniter
 *
 * @package Combustor
 * @author  Rougin Gutib <rougingutib@gmail.com>
 */
class CreateModelCommand extends AbstractCommand
{
    /**
     * @var string
     */
    protected $command = 'model';

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
                null,
                InputOption::VALUE_NONE,
                'Uses the camel case naming convention'
            )->addOption(
                'lowercase',
                null,
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
        $fileName = ucfirst(singular($input->getArgument('name')));

        $path = APPPATH . 'models' . DIRECTORY_SEPARATOR . $fileName . '.php';

        $info = [
            'name' => $fileName,
            'type' => 'model',
            'path' => $path
        ];

        $validator = new ModelValidator($input->getOption('camel'), $info);

        if ($validator->fails()) {
            $message = $validator->getMessage();

            return $output->writeln('<error>' . $message . '</error>');
        }

        $data = [
            'file' => $info,
            'isCamel' => $input->getOption('camel'),
            'name' => $input->getArgument('name'),
            'type' => $validator->getLibrary()
        ];

        $generator = new ModelGenerator($this->describe, $data);

        $result = $generator->generate();
        $model = $this->renderer->render('Model.tpl', $result);
        $message = 'The model "' . $fileName . '" has been created successfully!';

        $file = new File($path);

        $file->putContents($model);
        $file->close();

        return $output->writeln('<info>' . $message . '</info>');
    }
}
