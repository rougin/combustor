<?php

namespace Rougin\Combustor\Wildfire;

use Rougin\Combustor\Tools;
use Rougin\Describe\Describe;
use Rougin\Combustor\Templates\Controller;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateControllerCommand
{
    protected $input  = NULL;
    protected $output = NULL;

    /**
     * Integrate InputInterface and OutputInterface to the specified command
     * 
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    public function __construct(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
    }

    /**
     * Execute the command
     *
     * @return int
     */
    public function execute()
    {
        /**
         * Set the name for the controller
         */

        $name = plural($this->input->getArgument('name'));

        if ($this->input->getOption('keep')) {
            $name = $this->input->getArgument('name');
        }

        /**
         * Get the controller template
         */
        
        $controller = file_get_contents(__DIR__ . '/Templates/Controller.txt');
        
        /**
         * Get the columns from the specified name
         */

        require APPPATH . 'config/database.php';

        $db['default']['driver'] = $db['default']['dbdriver'];
        unset($db['default']['dbdriver']);

        $describe = new Describe($db['default']);
        $table = $describe->getInformationFromTable($this->input->getArgument('name'));

        if (empty($table)) {
            $message = 'The table "' . $this->input->getArgument('name') . '" does not exists in the database!';
            $this->output->writeln('<error>' . $message . '</error>');

            return;
        }

        $columnsOnCreate         = NULL;
        $columnsOnCreateCounter  = 0;
        $columnsOnEdit           = NULL;
        $columnsToValidate       = NULL;
        $counter                 = 0;
        $dropdownColumnsOnCreate = '$data = array();';
        $dropdownColumnsOnEdit   = '$data[\'[singular]\'] = $this->wildfire->find(\'[table]\', $id);';
        $dropdowns               = 0;
        $models                  = '\'[singular]\'';
        $selectColumns           = array('name', 'description', 'label');

        foreach ($table as $row) {
            if ($row->isAutoIncrement()) {
                continue;
            }

            $methodName = 'set_' . strtolower($row->getField());
            $methodName = ($this->input->getOption('camel')) ? camelize($methodName) : underscore($methodName);

            if ($counter != 0) {
                $columnsOnCreate   .= ($row->getField() != 'datetime_updated' && ! $row->isForeignKey()) ? '            ' : NULL;
                $columnsOnEdit     .= ($row->getField() != 'datetime_created' && ! $row->isForeignKey()) ? '            ' : NULL;

                if ( ! $row->isNull() && $row->getField() != 'password' && $row->getField() != 'datetime_created' && $row->getField() != 'datetime_updated') {
                    $columnsToValidate .= '     ';
                }

                if ($table[$counter + 1]->isForeignKey() && ! $table[$counter]->isForeignKey()) {
                    $columnsOnCreate .= "\n";
                    $columnsOnEdit   .= "\n";
                }
            }

            /**
             * Add validations per field
             */

            $rule = 'required';

            if ( ! $row->isNull() && $row->getField() != 'password' && $row->getField() != 'datetime_created' && $row->getField() != 'datetime_updated') {
                $label = strtolower(str_replace('_', ' ', $row->getField()));

                if (strpos($row->getField(), 'email') !== FALSE) {
                    $rule .= '|valid_email';
                }

                $columnsToValidate .= '$this->form_validation->set_rules(\'' . $row->getField() . '\', \'' . $label . '\', \'' . $rule . '\');' . "\n";
            }

            if ($row->isForeignKey()) {
                $referencedTable = singular(Tools::stripTableSchema($row->getReferencedTable()));

                if (strpos($models, ",\n" . '           \'' . $referencedTable . '\'') === FALSE) {
                    $models .= ",\n" . '            \'' . $referencedTable . '\'';
                }

                $foreignTableInformation = $describe->getInformationFromTable($row->getReferencedTable());
                $fieldDescription = $describe->getPrimaryKey($row->getReferencedTable());

                foreach ($foreignTableInformation as $foreignRow) {
                    if ($foreignRow->isForeignKey()) {
                        if (strpos($models, ",\n" . '           \'' . $foreignRow->getReferencedTable() . '\'') === FALSE) {
                            $models .= ",\n" . '            \'' . $foreignRow->getReferencedTable() . '\'';
                        }
                    }

                    $fieldDescription = in_array($foreignRow->getField(), $selectColumns) ? $foreignRow->getField() : $fieldDescription;
                }

                $dropdownColumn = '$data[\'' . plural(Tools::stripTableSchema($row->getReferencedTable())) . '\'] = $this->wildfire->get_all(\'' . $row->getReferencedTable() . '\')->as_dropdown(\'' . $fieldDescription . '\');';

                $dropdownColumnsOnCreate .= "\n\t\t" . $dropdownColumn;
                $dropdownColumnsOnEdit   .= "\n\t\t" . $dropdownColumn;

                if ($counter != 0) {
                    $columnsOnCreate .= "\t\t\t";
                    $columnsOnEdit   .= "\t\t\t";
                }

                $columnsOnCreate .= '$' . Tools::stripTableSchema($row->getReferencedTable()) . ' = $this->wildfire->find(\'' . $row->getReferencedTable() . '\', $this->input->post(\'' . $row->getField() . '\'));' . "\n";
                $columnsOnCreate .= '           $this->[singular]->' . $methodName . '($' . Tools::stripTableSchema($row->getReferencedTable()) . ');' . "\n\n";

                $columnsOnEdit .= '$' . Tools::stripTableSchema($row->getReferencedTable()) . ' = $this->wildfire->find(\'' . $row->getReferencedTable() . '\', $this->input->post(\'' . $row->getField() . '\'));' . "\n";
                $columnsOnEdit .= '         $[singular]->' . $methodName . '($' . Tools::stripTableSchema($row->getReferencedTable()) . ');' . "\n\n";

                continue;
            }

            if ($row->getField() == 'password') {
                $columnsOnCreate .= "\n" . file_get_contents(__DIR__ . '/../Templates/Miscellaneous/CheckCreatePassword.txt') . "\n\n";
                $columnsOnEdit   .= "\n" . file_get_contents(__DIR__ . '/../Templates/Miscellaneous/CheckEditPassword.txt') . "\n\n";

                $getMethodName = str_replace('set', 'get', $methodName);

                $columnsOnCreate = str_replace('[method]', $methodName, $columnsOnCreate);
                $columnsOnEdit   = str_replace(array('[method]', '[getMethod]'), array($methodName, $getMethodName), $columnsOnEdit);

                continue;
            }

            if ($row->getField() == 'datetime_created' || $row->getField() == 'datetime_updated') {
                $column = '\'now\'';
            } else {
                $column = '$this->input->post(\'' . $row->getField() . '\')';
            }

            if ($row->getField() == 'gender') {
                $dropdownColumn = '$data[\'' . plural('gender') . '\'] = array(\'male\' => \'Male\', \'female\' => \'Female\');';

                $dropdownColumnsOnCreate .= "\n\t\t" . $dropdownColumn;
                $dropdownColumnsOnEdit   .= "\n\t\t" . $dropdownColumn;
            }

            if ($row->getField() != 'datetime_updated') {
                $columnsOnCreate .= '$this->[singular]->' . $methodName . '(' . $column . ');' . "\n";
            }

            if ($row->getField() != 'datetime_created') {
                $columnsOnEdit .= '$[singular]->' . $methodName . '(' . $column . ');' . "\n";
            }

            $counter++;
        }

        /**
         * Search and replace the following keywords from the template
         */

        $search = array(
            '[models]',
            '[dropdownColumnsOnCreate]',
            '[dropdownColumnsOnEdit]',
            '[columnsOnCreate]',
            '[columnsOnEdit]',
            '[columnsToValidate]',
            '[controller]',
            '[controllerName]',
            '[plural]',
            '[pluralText]',
            '[singular]',
            '[singularText]',
            '[table]'
        );

        $replace = array(
            rtrim($models),
            rtrim($dropdownColumnsOnCreate),
            rtrim($dropdownColumnsOnEdit),
            rtrim($columnsOnCreate),
            rtrim($columnsOnEdit),
            rtrim($columnsToValidate),
            ucfirst(Tools::stripTableSchema($name)),
            ucfirst(Tools::stripTableSchema(str_replace('_', ' ', $name))),
            Tools::stripTableSchema(plural($name)),
            strtolower(plural($name)),
            Tools::stripTableSchema(singular($name)),
            strtolower(humanize(singular($name))),
            $this->input->getArgument('name')
        );

        $controller = str_replace($search, $replace, $controller);

        /**
         * Create a new file and insert the generated template
         */

        $name           = Tools::stripTableSchema($name);
        $controllerFile = ($this->input->getOption('lowercase')) ? strtolower($name) : ucfirst($name);
        $filename       = APPPATH . 'controllers/' . $controllerFile . '.php';

        if (file_exists($filename)) {
            $this->output->writeln('<error>The "' . $name . '" controller already exists!</error>');
        } else {
            $file = fopen($filename, 'wb');
            file_put_contents($filename, $controller);

            $this->output->writeln('<info>The controller "' . $name . '" has been created successfully!</info>');
        }
    }
}
