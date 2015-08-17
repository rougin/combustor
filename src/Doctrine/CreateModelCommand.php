<?php

namespace Rougin\Combustor\Doctrine;

use Rougin\Combustor\Tools;
use Rougin\Describe\Describe;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateModelCommand
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
        $this->input  = $input;
        $this->output = $output;
    }

    /**
     * Execute the command
     * 
     * @return int
     */
    public function execute()
    {
        $accessors       = NULL;
        $columns         = NULL;
        $counter         = 0;
        $dataTypes       = array('time', 'date', 'datetime', 'datetimetz');
        $indexes         = NULL;
        $indexesCounter  = 0;
        $keywords        = NULL;
        $keywordsCounter = NULL;
        $mutators        = NULL;
        $mutatorsCounter = 0;
        $name            = singular($this->input->getArgument('name'));

        $selectColumns = array('name', 'description', 'label');

        /**
         * Get the model template
         */

        $model = file_get_contents(__DIR__ . '/Templates/Model.txt');

        /**
         * Get the columns from the specified name
         */

        require APPPATH . 'config/database.php';

        $db['default']['driver'] = $db['default']['dbdriver'];
        unset($db['default']['dbdriver']);

        $describe = new Describe($db['default']);
        $tableInformation = $describe->getInformationFromTable($this->input->getArgument('name'));

        if (empty($tableInformation)) {
            $message = 'The table "' . $this->input->getArgument('name') . '" does not exists in the database!';
            $this->output->writeln('<error>' . $message . '</error>');

            return;
        }

        foreach ($tableInformation as $row) {
            $accessors .= ($counter != 0) ? '   ' : NULL;
            $columns   .= ($counter != 0) ? '   ' : NULL;
            $mutators  .= ($mutatorsCounter != 0) ? '   ' : NULL;

            $nullable = ($row->isNull()) ? 'TRUE' : 'FALSE';
            $unique   = ($row->isUnique()) ? 'TRUE' : 'FALSE';

            $type = Tools::getDataType($row->getDataType());
            $length = NULL;

            if ($row->getLength()) {
                $length = ', length=' . $row->getLength();
            }

            /**
             * Generate the columns and indexes
             */

            $columns .= '/**' . "\n";

            if ($row->isPrimaryKey()) {
                $autoIncrement = ($row->isAutoIncrement()) ? '@GeneratedValue' : NULL;

                $columns .= '    * @Id ' . $autoIncrement . "\n";
                $columns .= '    * @Column(type="' . $type . '"' . $length . ', nullable=' . $nullable . ', unique=' . $unique . ')' . "\n";
            } else if ($row->isForeignKey()) {
                $indexes .= ($indexesCounter != 0) ? ' *        ' : NULL;

                $indexes .= '@index(name="' . $row->getField() . '", columns={"' . $row->getField() . '"}),' . "\n";
                $type     = '\\' . ucfirst($row->getReferencedTable());

                $columns .= '    * @ManyToOne(targetEntity="' . ucfirst($row->getReferencedTable()) . '", cascade={"persist"})' . "\n";
                $columns .= '    * @JoinColumns({' . "\n";
                $columns .= '    *  @JoinColumn(name="' . $row->getField() . '", referencedColumnName="' . $row->getReferencedField() . '", nullable=' . $nullable . ', onDelete="cascade")' . "\n";
                $columns .= '    * })' . "\n";

                $indexesCounter++;
            } else {
                $columns .= '    * @Column(type="' . $type . '"' . $length . ', nullable=' . $nullable . ', unique=' . $unique . ')' . "\n";

                if ($row->getField() != 'datetime_created' && $row->getField() != 'datetime_updated' && $row->getField() != 'password') {
                    $keywords .= ($keywordsCounter != 0) ? '        ' : NULL;
                    $keywords .= '\'[firstLetter].' . $row->getField() . '\'' . ",\n";

                    $keywordsCounter++;
                }
            }

            $columns .= '    */' . "\n";
            $columns .= '   protected $' . $row->getField() . ';' . "\n\n";

            /**
             * Generate the accessors
             */

            $methodName = 'get_' . $row->getField();
            $methodName = ($this->input->getOption('camel')) ? camelize($methodName) : underscore($methodName);
            
            $accessor = file_get_contents(__DIR__ . '/Templates/Miscellaneous/Accessor.txt');
            
            $search  = array('[field]', '[type]', '[method]');
            $replace = array($row->getField(), $type, $methodName);

            $accessors .= str_replace($search, $replace, $accessor) . "\n\n";

            /**
             * Generate the mutators
             */

            if ( ! $row->isAutoIncrement()) {
                $class         = '\\' . ucfirst($name);
                $classVariable = ($row->isForeignKey()) ? '\\' . ucfirst(singular(Tools::stripTableSchema($row->getReferencedTable()))) . ' ' : NULL;
                
                $methodName = 'set_' . $row->getField();
                $methodName = ($this->input->getOption('camel')) ? camelize($methodName) : underscore($methodName);

                $nullable = ($row->isNull()) ? ' = NULL' : NULL;

                $mutator = file_get_contents(__DIR__ . '/Templates/Miscellaneous/Mutator.txt');

                if (in_array(Tools::getDataType($row->getDataType()), $dataTypes)) {
                    $mutator = str_replace('$this->[field] = $[field];', '$this->[field] = new \DateTime($[field]);', $mutator);
                }

                $search  = array('[field]', '[type]', '[method]', '[classVariable]', '[class]', '[nullable]');
                $replace = array($row->getField(), $type, $methodName, $classVariable, $class, $nullable);
                
                $mutators .= str_replace($search, $replace, $mutator) . "\n\n";

                $mutatorsCounter++;
            }

            $counter++;
        }

        /**
         * Search and replace the following keywords from the template
         */

        $search = array(
            '[indexes]',
            '[columns]',
            '[accessors]',
            '[mutators]',
            '[singular]',
            '[firstLetter]',
            '[model]',
            '[table]'
        );

        $replace = array(
            rtrim(substr($indexes, 0, -2)),
            rtrim($columns),
            rtrim($accessors),
            rtrim($mutators),
            $name,
            substr($this->input->getArgument('name'), 0, 1),
            ucfirst($name),
            $this->input->getArgument('name')
        );

        $model = str_replace($search, $replace, $model);

        /**
         * Create a new file and insert the generated template
         */

        $modelFile = ($this->input->getOption('lowercase')) ? strtolower($name) : ucfirst($name);
        $filename = APPPATH . 'models/' . $modelFile . '.php';

        if (file_exists($filename)) {
            $this->output->writeln('<error>The "' . $name . '" model already exists!</error>');
        } else {
            $file = fopen($filename, 'wb');
            file_put_contents($filename, $model);

            $this->output->writeln('<info>The model "' . $name . '" has been created successfully!</info>');
        }
    }
}
