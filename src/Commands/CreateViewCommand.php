<?php

namespace Rougin\Combustor\Commands;

use Rougin\Describe\Describe;
use Rougin\Combustor\Common\File;
use Rougin\Combustor\Common\Tools;
use Rougin\Combustor\Common\FileCollection;
use Rougin\Combustor\Generator\ViewGenerator;
use Rougin\Combustor\Validator\ViewValidator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Create View Command
 *
 * Creates a list of views for CodeIgniter
 * 
 * @package Combustor
 * @author  Rougin Royce Gutib <rougingutib@gmail.com>
 */
class CreateViewCommand extends AbstractCommand
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
        if ( ! Tools::isCommandEnabled() && ! Tools::hasLayout()) {
            return false;
        }

        return true;
    }

    /**
     * Set the configurations of the specified command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('create:view')
            ->setDescription('Create a new view')
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'Name of the view folder'
            )->addOption(
                'bootstrap',
                NULL,
                InputOption::VALUE_NONE,
                'Includes the Bootstrap CSS/JS Framework tags'
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
            );
    }

    /**
     * Executes the command.
     * 
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     * @return object|OutputInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = ( ! $input->getOption('keep'))
            ? Tools::stripTableSchema(plural($input->getArgument('name')))
            : Tools::stripTableSchema($input->getArgument('name'));

        $validator = new ViewValidator($name);

        if ($validator->fails()) {
            $message = $validator->getMessage();

            return $output->writeln('<error>'.$message.'</error>');
        }

        $data = [
            'isBootstrap' => $input->getOption('bootstrap'),
            'isCamel' => $input->getOption('camel'),
            'name' => $name
        ];

        $generator = new ViewGenerator($this->describe, $data);

        $result = $generator->generate();

        $results = [
            'create' => $this->renderer->render('Views/create.template', $result),
            'edit' => $this->renderer->render('Views/edit.template', $result),
            'index' => $this->renderer->render('Views/index.template', $result),
            'show' => $this->renderer->render('Views/show.template', $result)
        ];

        $files = new FileCollection;
        $filePath = APPPATH.'views/'.$name;

        $files
            ->add(new File($filePath.'/create.php', 'wb'), 'create')
            ->add(new File($filePath.'/edit.php', 'wb'), 'edit')
            ->add(new File($filePath.'/index.php', 'wb'), 'index')
            ->add(new File($filePath.'/show.php', 'wb'), 'show');

        foreach ($results as $key => $value) {
            $files->get($key)->putContents($value);
        }

        $files->close();

        $message = 'The views folder "'.$name.
            '" has been created successfully!';

        return $output->writeln('<info>'.$message.'</info>');
    }
}
