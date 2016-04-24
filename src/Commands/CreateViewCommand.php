<?php

namespace Rougin\Combustor\Commands;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Rougin\Combustor\Common\Tools;
use Rougin\Combustor\Generator\ViewGenerator;
use Rougin\Combustor\Validator\ViewValidator;

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
        return Tools::isCommandEnabled() && Tools::hasLayout();
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
                'Name of the table'
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
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return object|\Symfony\Component\Console\Output\OutputInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = Tools::stripTableSchema(plural($input->getArgument('name')));

        if ($input->getOption('keep')) {
            $name = Tools::stripTableSchema($input->getArgument('name'));
        }

        $validator = new ViewValidator($name);

        if ($validator->fails()) {
            $message = $validator->getMessage();

            return $output->writeln('<error>' . $message . '</error>');
        }

        $data = [
            'isBootstrap' => $input->getOption('bootstrap'),
            'isCamel' => $input->getOption('camel'),
            'name' => $input->getArgument('name')
        ];

        $generator = new ViewGenerator($this->describe, $data);

        $result = $generator->generate();

        $results = [
            'create' => $this->renderer->render('Views/create.template', $result),
            'edit' => $this->renderer->render('Views/edit.template', $result),
            'index' => $this->renderer->render('Views/index.template', $result),
            'show' => $this->renderer->render('Views/show.template', $result)
        ];

        $filePath = APPPATH . 'views/' . $name;

        $file = fopen($filePath . '/create.php', 'wb');
        file_put_contents($filePath . '/create.php', $results['create']);
        fclose($file);

        $file = fopen($filePath . '/edit.php', 'wb');
        file_put_contents($filePath . '/edit.php', $results['edit']);
        fclose($file);

        $file = fopen($filePath . '/index.php', 'wb');
        file_put_contents($filePath . '/index.php', $results['index']);
        fclose($file);

        $file = fopen($filePath . '/show.php', 'wb');
        file_put_contents($filePath . '/show.php', $results['show']);
        fclose($file);

        $message = 'The views folder "' . $name . '" has been created successfully!';

        return $output->writeln('<info>' . $message . '</info>');
    }
}
