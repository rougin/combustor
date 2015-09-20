<?php

namespace Rougin\Combustor\Commands;

use Rougin\Blueprint\AbstractCommand;
use Rougin\Combustor\Tools;
use Rougin\Describe\Describe;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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
        if (
            (file_exists(APPPATH . 'libraries/Wildfire.php') ||
            file_exists(APPPATH . 'libraries/Doctrine.php')) &&
            (file_exists(APPPATH . 'views/layout/header.php') &&
            file_exists(APPPATH . 'views/layout/footer.php'))
        ) {
            return TRUE;
        }

        return FALSE;
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
        $viewDirectory = ( ! $input->getOption('keep'))
            ? plural($input->getArgument('name'))
            : $input->getArgument('name');

        $viewDirectory = Tools::stripTableSchema($viewDirectory);
        $filePath = APPPATH . 'views/' . $viewDirectory;
        $folderExists = ! @mkdir($filePath, 0775, TRUE);

        if ($folderExists) {
            $message = 'The "' . $input->getArgument('name') . '" views folder already exists!';

            return $output->writeln('<error>' . $message . '</error>');
        }

        require APPPATH . 'config/database.php';

        $db['default']['driver'] = $db['default']['dbdriver'];
        unset($db['default']['dbdriver']);

        $describe = new Describe($db['default']);

        /**
         * Integrate Bootstrap if enabled
         */

        if ($input->getOption('bootstrap')) {
            $data['isBootstrap'] = TRUE;

            $data['bootstrap'] = [
                'button' => 'btn btn-default',
                'buttonPrimary' => 'btn btn-primary',
                'formControl' => 'form-control',
                'formGroup' => 'form-group col-lg-12 col-md-12 ' .
                    'col-sm-12 col-xs-12',
                'label' => 'control-label',
                'table' => 'table table table-striped table-hover',
                'textRight' => 'text-right'
            ];
        }

        $data['camel'] = [];
        $data['underscore'] = [];
        $data['foreignKeys'] = [];
        $data['primaryKeys'] = [];

        $data['name'] = $input->getArgument('name');
        $data['plural'] = plural($data['name']);
        $data['singular'] = singular($data['name']);

        $data['primaryKey'] = 'get_' . $describe->getPrimaryKey(
            $input->getArgument('name')
        );

        if ($input->getOption('camel')) {
            $data['primaryKey'] = camelize($data['primaryKey']);
        }

        /**
         * Get the columns from the specified name
         */

        $data['columns'] = $describe->getInformationFromTable(
            $input->getArgument('name')
        );

        foreach ($data['columns'] as $column) {
            $field = strtolower($column->getField());
            $accessor = 'get_' . $field;
            $mutator = 'set_' . $field;

            if ($input->getOption('camel')) {
                $data['camel'][$field] = array(
                    'field' => lcfirst(camelize($field)),
                    'accessor' => lcfirst(camelize($accessor)),
                    'mutator' => lcfirst(camelize($mutator))
                );
            } else {
                $data['underscore'][$field] = array(
                    'field' => lcfirst(underscore($field)),
                    'accessor' => lcfirst(underscore($accessor)),
                    'mutator' => lcfirst(underscore($mutator))
                );
            }

            if ($column->isForeignKey()) {
                $referencedTable = Tools::stripTableSchema(
                    $column->getReferencedTable()
                );

                $data['foreignKeys'][$field] = plural(
                    $referencedTable
                );

                $singular = $field . '_singular';

                $data['foreignKeys'][$singular] = singular(
                    $referencedTable
                );

                $data['primaryKeys'][$field] = 'get_' . $describe->getPrimaryKey(
                    $referencedTable
                );

                if ($input->getOption('camel')) {
                    $data['primaryKeys'][$field] = camelize(
                        $data['primaryKeys'][$field]
                    );
                }
            }
        }

        /**
         * Create the files
         */

        $create = $this->renderer->render('Views/Create.php', $data);
        $edit = $this->renderer->render('Views/Edit.php', $data);
        $index = $this->renderer->render('Views/Index.php', $data);
        $show = $this->renderer->render('Views/Show.php', $data);

        $createFile = fopen($filePath . '/create.php', 'wb');
        $editFile = fopen($filePath . '/edit.php', 'wb');
        $indexFile = fopen($filePath . '/index.php', 'wb');
        $showFile = fopen($filePath . '/show.php', 'wb');

        file_put_contents($filePath . '/create.php', $create);
        file_put_contents($filePath . '/edit.php', $edit);
        file_put_contents($filePath . '/index.php', $index);
        file_put_contents($filePath . '/show.php', $show);

        $message = 'The views folder "' . 
            plural($input->getArgument('name')) .
            '" has been created successfully!';

        return $output->writeln('<info>' . $message . '</info>');
    }
}
