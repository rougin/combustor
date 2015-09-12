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
            ->setName('create:model')
            ->setDescription('Create a new model')
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'Name of the model'
            )->addOption(
                'camel',
                NULL,
                InputOption::VALUE_NONE,
                'Use the camel case naming convention'
            )->addOption(
                'doctrine',
                NULL,
                InputOption::VALUE_NONE,
                'Generate a model based on Doctrine'
            )->addOption(
                'lowercase',
                NULL,
                InputOption::VALUE_NONE,
                'Keep the first character of the name to lowercase'
            )->addOption(
                'wildfire',
                NULL,
                InputOption::VALUE_NONE,
                'Generate a model based on Wildfire'
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
            'doctrine' => $input->getOption('doctrine') ? TRUE : FALSE,
            'wildfire' => $input->getOption('wildfire') ? TRUE : FALSE
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
     * Generates a Wildfire-based or Doctrine-based model.
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
        $data['indexes'] = [];
        $data['isCamel'] = ($input->getOption('camel')) ? TRUE : FALSE;
        $data['name'] = $input->getArgument('name');
        $data['primaryKeys'] = [];
        $data['type'] = $type;
        $data['underscore'] = [];

        $fileName = ucfirst($data['name']) . '.php';
        $path = APPPATH . 'models' . DIRECTORY_SEPARATOR . $fileName;

        $data['columns'] = $describe->getInformationFromTable($data['name']);
        $data['primaryKey'] = $describe->getPrimaryKey($data['name']);

        if (file_exists($path)) {
            $message = 'The "' . $data['name'] . '" controller already exists!';

            return $output->writeln('<error>' . $message . '</error>');
        }

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
                $field = $column->getField();

                array_push($data['indexes'], $field);

                $data['primaryKeys'][$field] = 'get_' . $describe->getPrimaryKey(
                    $column->getReferencedTable()
                );

                if ($input->getOption('camel')) {
                    $data['primaryKeys'][$field] = camelize(
                        $data['primaryKeys'][$field]
                    );
                }
            }

            $column->setReferencedTable(
                Tools::stripTableSchema($column->getReferencedTable())
            );
        }

        $model = $this->renderer->render('Model.php', $data);

        $file = fopen($path, 'wb');
        file_put_contents($path, $model);

        fclose($file);

        $message = 'The model "' . $data['name'] .
            '" has been created successfully!';

        return $output->writeln('<info>' . $message . '</info>');
    }
}
