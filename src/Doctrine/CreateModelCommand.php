<?php namespace Combustor\Doctrine;

use Describe\Describe;
use Combustor\Tools\Inflect;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateModelCommand extends Command
{

	/**
	 * Get the data type of the specified column
	 * 
	 * @param  string $type
	 * @return string
	 */
	protected function _getDataType($type)
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

	/**
	 * Set the configurations of the specified command
	 */
	protected function configure()
	{
		$this->setName('doctrine:model')
			->setDescription('Create a new Doctrine-based model')
			->addArgument(
				'name',
				InputArgument::REQUIRED,
				'Name of the model'
			)->addOption(
				'lowercase',
				null,
				InputOption::VALUE_NONE,
				'Keep the first character of the name to lowercase'
			)->addOption(
				'camel',
				NULL,
				InputOption::VALUE_NONE,
				'Use the camel case naming convention for the accessor and mutators'
			);
	}

	/**
	 * Execute the command
	 * 
	 * @param  InputInterface  $input
	 * @param  OutputInterface $output
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
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
		$name            = Inflect::singularize($input->getArgument('name'));
		$primaryKey      = NULL;

		$repository         = $name . '_repository';
		$singularRepository = ($input->getOption('camel')) ? Inflect::camelize($repository) : Inflect::underscore($repository);

		$selectColumns = array('name', 'description', 'label');

		/**
		 * Get the model template
		 */
		
		$doctrineDirectory = str_replace('/Doctrine', '', __DIR__);
		$model = file_get_contents($doctrineDirectory . '/Templates/Doctrine/Model.txt');

		/**
		 * Get the columns from the specified name
		 */

		require APPPATH . 'config/database.php';

		$db['default']['driver'] = $db['default']['dbdriver'];
		unset($db['default']['dbdriver']);

		$describe = new Describe($db['default']);
		$tableInformation = $describe->getInformationFromTable($input->getArgument('name'));

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
			$methodName = ($input->getOption('camel')) ? Inflect::camelize($methodName) : Inflect::underscore($methodName);
			
			$primaryKey = ($row->key == 'PRI') ? $methodName : $primaryKey;
			
			$accessor = file_get_contents($doctrineDirectory . '/Templates/Doctrine/Miscellaneous/Accessor.txt');
			
			$search  = array('[field]', '[type]', '[method]');
			$replace = array($row->field, $type, $methodName);

			$accessors .= str_replace($search, $replace, $accessor) . "\n\n";

			/**
			 * The column to be displayed in the select() public method
			 */

			if (in_array($row->field, $selectColumns)) {
				$model = str_replace('/* Column to be displayed in the dropdown */', $methodName . '()', $model);
			} else {
				$model = str_replace('/* Column to be displayed in the dropdown */', '[primaryKey]()', $model);
			}

			/**
			 * Generate the mutators
			 */

			if ($row->extra != 'auto_increment') {
				$class         = '\\' . ucfirst($name);
				$classVariable = ($row->key == 'MUL') ? '\\' . ucfirst($row->referencedTable) . ' ' : NULL;
				
				$methodName = 'set_' . $row->field;
				$methodName = ($input->getOption('camel')) ? Inflect::camelize($methodName) : Inflect::underscore($methodName);

				$nullable = ($row->isNull) ? ' = NULL' : NULL;

				$mutator = file_get_contents($doctrineDirectory . '/Templates/Doctrine/Miscellaneous/Mutator.txt');

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
			'[singularRepository]',
			'[indexes]',
			'[columns]',
			'[keywords]',
			'[accessors]',
			'[mutators]',
			'[primaryKey]',
			'[plural]',
			'[singular]',
			'[firstLetter]',
			'[model]'
		);

		$replace = array(
			$singularRepository,
			rtrim(substr($indexes, 0, -2)),
			rtrim($columns),
			rtrim(substr($keywords, 0, -2)),
			rtrim($accessors),
			rtrim($mutators),
			$primaryKey,
			Inflect::pluralize($input->getArgument('name')),
			$name,
			substr($input->getArgument('name'), 0, 1),
			ucfirst($name)
		);

		$model = str_replace($search, $replace, $model);

		/**
		 * Create a new file and insert the generated template
		 */

		$modelFile = ($input->getOption('lowercase')) ? strtolower($name) : ucfirst($name);

		$filename = APPPATH . 'models/' . $modelFile . '.php';

		if (file_exists($filename)) {
			$output->writeln('<error>The ' . $name . ' model already exists!</error>');
			
			exit();
		}

		$file = fopen($filename, 'wb');
		file_put_contents($filename, $model);

		$output->writeln('<info>The model "' . $name . '" has been created successfully!</info>');
	}

}