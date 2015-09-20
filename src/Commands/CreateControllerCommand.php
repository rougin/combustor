<?php

namespace Rougin\Combustor\Commands;

use Rougin\Blueprint\AbstractCommand;
use Rougin\Combustor\Tools;
use Rougin\Combustor\Validator;
use Rougin\Describe\Describe;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Twig_Environment;

/**
 * Create Controller Command
 *
 * Generates a Wildfire or Doctrine-based controller for CodeIgniter
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
        if (
            file_exists(APPPATH . 'libraries/Wildfire.php') ||
            file_exists(APPPATH . 'libraries/Doctrine.php')
        ) {
            return TRUE;
        }

        return FALSE;
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
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     * @return object|OutputInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        require APPPATH . '/config/database.php';

        $db['default']['driver'] = $db['default']['dbdriver'];
        unset($db['default']['dbdriver']);

        $describe = new Describe($db['default']);

        $doesExists = [
            'camel' => $input->getOption('camel') ? TRUE : FALSE,
            'doctrine' => $input->getOption('doctrine'),
            'wildfire' => $input->getOption('wildfire')
        ];

        $validator = new Validator($describe, $doesExists);
        $type = $validator->getType();

        if ($validator->hasError()) {
            $message = $validator->getMessage();

            return $output->writeln('<error>' . $message . '</error>');
        }

        return $this->generate($type, $describe, $input, $output);
    }

    /**
     * Generates a Wildfire-based or Doctrine-based controller.
     * 
     * @param  string           $type
     * @param  Describe         $describe
     * @param  InputInterface   $input
     * @param  OutputInterface  $output
     * @return object|OutputInterface
     */
    protected function generate($type, $describe, $input, $output)
    {
        $data['camel'] = [];
        $data['columns'] = [];
        $data['dropdowns'] = [];
        $data['foreignKeys'] = [];
        $data['isCamel'] = ($input->getOption('camel')) ? TRUE : FALSE;
        $data['models'] = [$input->getArgument('name')];
        $data['name'] = $input->getArgument('name');
        $data['plural'] = plural($data['name']);
        $data['singular'] = singular($data['name']);
        $data['type'] = $type;
        $data['underscore'] = [];

        $fileName = ucfirst($data['name']) . '.php';

        if ( ! $input->getOption('keep')) {
            $data['name'] = plural($data['name']);
            $fileName = plural(ucfirst($data['name'])) . '.php';
        }

        $columnFields = ['name', 'description', 'label'];
        $path = APPPATH . 'controllers' . DIRECTORY_SEPARATOR . $fileName;

        if (file_exists($path)) {
            $message = 'The "' . $data['name'] . '" controller already exists!';

            return $output->writeln('<error>' . $message . '</error>');
        }

        $table = $describe->getInformationFromTable(
            $input->getArgument('name')
        );

        foreach ($table as $column) {
            if ($column->isAutoIncrement()) {
                continue;
            }

            $field = strtolower($column->getField());
            $method = 'set_' . $field;

            $data['camel'][$field] = lcfirst(camelize($method));
            $data['underscore'][$field] = underscore($method);

            array_push($data['columns'], $field);

            if ($column->isForeignKey()) {
                $referencedTable = Tools::stripTableSchema(
                    $column->getReferencedTable()
                );

                $data['foreignKeys'][$field] = $referencedTable;

                array_push($data['models'], $referencedTable);

                $dropdown = [
                    'list' => plural($referencedTable),
                    'table' => $referencedTable,
                    'field' => $field
                ];

                if (!in_array($field, $columnFields)) {
                    $dropdown['field'] = $describe->getPrimaryKey(
                        $referencedTable
                    );
                }

                array_push($data['dropdowns'], $dropdown);
            }
        }

        $controller = $this->renderer->render('Controller.php', $data);

        $file = fopen($path, 'wb');
        file_put_contents($path, $controller);

        fclose($file);

        $message = 'The controller "' . $data['name'] .
            '" has been created successfully!';

        return $output->writeln('<info>' . $message . '</info>');
    }
}
