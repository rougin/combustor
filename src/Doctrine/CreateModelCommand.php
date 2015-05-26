<?php namespace Rougin\Combustor\Doctrine;

use Symfony\Component\Console\Command\Command;
use Rougin\Combustor\Tools;
use Rougin\Describe\Describe;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateModelCommand extends Command
{

	private $_input  = NULL;
	private $_output = NULL;

	/**
	 * Integrate InputInterface and OutputInterface to the specified command
	 * 
	 * @param InputInterface  $input
	 * @param OutputInterface $output
	 */
	public function __construct(InputInterface $input, OutputInterface $output)
	{
		$this->_input  = $input;
		$this->_output = $output;
	}

	/**
	 * Execute the command
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
		$name            = Inflect::singularize($this->_input->getArgument('name'));

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
		$tableInformation = $describe->getInformationFromTable($this->_input->getArgument('name'));

		foreach ($tableInformation as $row) {
			$accessors .= ($counter != 0) ? '	' : NULL;
			$columns   .= ($counter != 0) ? '	' : NULL;
			$mutators  .= ($mutatorsCounter != 0) ? '	' : NULL;

			$nullable = ($row->isNull) ? 'TRUE' : 'FALSE';
			$unique   = ($row->key == 'UNI') ? 'TRUE' : 'FALSE';

			$type = $this->_getDataType($row->type);

			/**
			 * The data type is an integer or string? Set the length of the specified data type
			 */

			if ((strpos($row->type, 'int') !== FALSE) || (strpos($row->type, 'varchar') !== FALSE)) {
				$length =  ', length=' . str_replace(array($type, '(', ')', 'varchar', 'int'), array('', '', '', '', ''), $row->type);
			} else {
				$length = NULL;
			}

			/**
			 * Generate the columns and indexes
			 */

			$columns .= '/**' . "\n";

			if ($row->key == 'PRI') {
				$autoIncrement = ($row->extra == 'auto_increment') ? '@GeneratedValue' : NULL;

				$columns .= '	 * @Id ' . $autoIncrement . "\n";
				$columns .= '	 * @Column(type="' . $type . '"' . $length . ', nullable=' . $nullable . ', unique=' . $unique . ')' . "\n";
			} elseif ($row->key == 'MUL') {
				$indexes .= ($indexesCounter != 0) ? ' *   		' : NULL;

				$indexes .= '@index(name="' . $row->field . '", columns={"' . $row->field . '"}),' . "\n";
				$type     = '\\' . ucfirst($row->referencedTable);

				$columns .= '	 * @ManyToOne(targetEntity="' . ucfirst($row->referencedTable) . '", cascade={"persist"})' . "\n";
				$columns .= '	 * @JoinColumns({' . "\n";
				$columns .= '	 * 	@JoinColumn(name="' . $row->field . '", referencedColumnName="' . $row->referencedColumn . '", nullable=' . $nullable . ', onDelete="cascade")' . "\n";
				$columns .= '	 * })' . "\n";

				$indexesCounter++;
			} else {
				$columns .= '	 * @Column(type="' . $type . '"' . $length . ', nullable=' . $nullable . ', unique=' . $unique . ')' . "\n";

				if ($row->field != 'datetime_created' && $row->field != 'datetime_updated' && $row->field != 'password') {
					$keywords .= ($keywordsCounter != 0) ? '		' : NULL;
					$keywords .= '\'[firstLetter].' . $row->field . '\'' . ",\n";

					$keywordsCounter++;
				}
			}

			$columns .= '	 */' . "\n";
			$columns .= '	protected $' . $row->field . ';' . "\n\n";

			/**
			 * Generate the accessors
			 */

			$methodName = 'get_' . $row->field;
			$methodName = ($this->_input->getOption('camel')) ? Inflect::camelize($methodName) : Inflect::underscore($methodName);
			
			$accessor = file_get_contents(__DIR__ . '/Templates/Miscellaneous/Accessor.txt');
			
			$search  = array('[field]', '[type]', '[method]');
			$replace = array($row->field, $type, $methodName);

			$accessors .= str_replace($search, $replace, $accessor) . "\n\n";

			/**
			 * Generate the mutators
			 */

			if ($row->extra != 'auto_increment') {
				$class         = '\\' . ucfirst($name);
				$classVariable = ($row->key == 'MUL') ? '\\' . ucfirst($row->referencedTable) . ' ' : NULL;
				
				$methodName = 'set_' . $row->field;
				$methodName = ($this->_input->getOption('camel')) ? Inflect::camelize($methodName) : Inflect::underscore($methodName);

				$nullable = ($row->isNull) ? ' = NULL' : NULL;

				$mutator = file_get_contents(__DIR__ . '/Templates/Miscellaneous/Mutator.txt');

				if (in_array($this->_getDataType($row->type), $dataTypes)) {
					$mutator = str_replace('$this->[field] = $[field];', '$this->[field] = new \DateTime($[field]);', $mutator);
				}

				$search  = array('[field]', '[type]', '[method]', '[classVariable]', '[class]', '[nullable]');
				$replace = array($row->field, $type, $methodName, $classVariable, $class, $nullable);
				
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
			'[model]'
		);

		$replace = array(
			rtrim(substr($indexes, 0, -2)),
			rtrim($columns),
			rtrim($accessors),
			rtrim($mutators),
			$name,
			substr($this->_input->getArgument('name'), 0, 1),
			ucfirst($name)
		);

		$model = str_replace($search, $replace, $model);

		/**
		 * Create a new file and insert the generated template
		 */

		$modelFile = ($this->_input->getOption('lowercase')) ? strtolower($name) : ucfirst($name);

		$filename = APPPATH . 'models/' . $modelFile . '.php';

		if (file_exists($filename)) {
			$this->_output->writeln('<error>The ' . $name . ' model already exists!</error>');
			
			exit();
		}

		$file = fopen($filename, 'wb');
		file_put_contents($filename, $model);

		$this->_output->writeln('<info>The model "' . $name . '" has been created successfully!</info>');
	}

	/**
	 * Get the data type of the specified column
	 * 
	 * @param  string $type
	 * @return string
	 */
	private function _getDataType($type)
	{
		if (strpos($type, 'array') !== FALSE) $type = 'array';
		elseif (strpos($type, 'bigint') !== FALSE) $type = 'bigint';
		elseif (strpos($type, 'blob') !== FALSE) $type = 'blob';
		elseif (strpos($type, 'boolean') !== FALSE) $type = 'boolean';
		elseif (strpos($type, 'datetime') !== FALSE || strpos($type, 'timestamp') !== FALSE) $type = 'datetime';
		elseif (strpos($type, 'datetimetz') !== FALSE) $type = 'datetimetz';
		elseif (strpos($type, 'date') !== FALSE) $type = 'date';
		elseif (strpos($type, 'decimal') !== FALSE || strpos($type, 'double') !== FALSE) $type = 'decimal';
		elseif (strpos($type, 'float') !== FALSE) $type = 'float';
		elseif (strpos($type, 'guid') !== FALSE) $type = 'guid';
		elseif (strpos($type, 'int') !== FALSE) $type = 'integer';
		elseif (strpos($type, 'json_array') !== FALSE) $type = 'json_array';
		elseif (strpos($type, 'object') !== FALSE) $type = 'object';
		elseif (strpos($type, 'simple_array') !== FALSE) $type = 'simple_array';
		elseif (strpos($type, 'smallint') !== FALSE) $type = 'smallint';
		elseif (strpos($type, 'text') !== FALSE) $type = 'text';
		elseif (strpos($type, 'time') !== FALSE) $type = 'time';
		elseif (strpos($type, 'varchar') !== FALSE) $type = 'string';

		return $type;
	}

}