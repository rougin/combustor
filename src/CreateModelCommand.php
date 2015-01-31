<?php namespace Combustor;

use Combustor\Tools\Inflect;
use Combustor\Tools\GetColumns;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateModelCommand extends Command
{

	/**
	 * Create a model based in Doctrine
	 * 
	 * @param  InputInterface  $input
	 * @param  OutputInterface $output
	 */
	protected function _doctrine(InputInterface $input, OutputInterface $output)
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
		
		$model = file_get_contents(__DIR__ . '/Templates/Doctrine/Model.txt');
		
		/**
		 * Get the columns from the specified name
		 */

		$databaseColumns = new GetColumns($input->getArgument('name'), $output);

		foreach ($databaseColumns->result() as $row) {
			$accessors .= ($counter != 0) ? '	' : NULL;
			$columns   .= ($counter != 0) ? '	' : NULL;
			$mutators  .= ($mutatorsCounter != 0) ? '	' : NULL;

			$nullable   = ($row->Null == 'YES') ? 'TRUE' : 'FALSE';
			$unique     = ($row->Key == 'UNI') ? 'TRUE' : 'FALSE';

			$type       = $this->_getDataType($row->Type);

			/**
			 * The data type is an integer or string? Set the length of the specified data type
			 */

			if ((strpos($row->Type, 'int') !== FALSE) || (strpos($row->Type, 'varchar') !== FALSE)) {
				$length =  ', length=' . str_replace(array($type, '(', ')', 'varchar', 'int'), array('', '', '', '', ''), $row->Type);
			} else {
				$length = NULL;
			}

			/**
			 * Generate the columns and indexes
			 */

			$columns .= '/**' . "\n";

			if ($row->Key == 'PRI') {
				$autoIncrement = ($row->Extra == 'auto_increment') ? '@GeneratedValue' : NULL;

				$columns .= '	 * @Id ' . $autoIncrement . "\n";
				$columns .= '	 * @Column(type="' . $type . '"' . $length . ', nullable=' . $nullable . ', unique=' . $unique . ')' . "\n";
			} elseif ($row->Key == 'MUL') {
				$indexes .= ($indexesCounter != 0) ? ' *   		' : NULL;

				$entity   = ucfirst(str_replace('_id', '', $row->Field));
				$indexes .= '@index(name="' . $row->Field . '", columns={"' . $row->Field . '"}),' . "\n";
				$type     = '\\' . ucfirst($entity);

				$columns .= '	 * @ManyToOne(targetEntity="' . $entity . '", cascade={"persist"})' . "\n";
				$columns .= '	 * @JoinColumns({' . "\n";
				$columns .= '	 *   @JoinColumn(name="' . $row->Field . '", referencedColumnName="' . $row->Field . '", nullable=' . $nullable . ', onDelete="cascade")' . "\n";
				$columns .= '	 * })' . "\n";

				$indexesCounter++;
			} else {
				$columns .= '	 * @Column(type="' . $type . '"' . $length . ', nullable=' . $nullable . ', unique=' . $unique . ')' . "\n";

				if ($row->Field != 'datetime_created' && $row->Field != 'datetime_updated' && $row->Field != 'password') {
					$keywords .= ($keywordsCounter != 0) ? '		' : NULL;
					$keywords .= '\'[firstLetter].' . $row->Field . '\'' . ",\n";

					$keywordsCounter++;
				}
			}

			$columns .= '	 */' . "\n";
			$columns .= '	protected $' . $row->Field . ';' . "\n\n";

			/**
			 * Generate the accessors
			 */

			$methodName = 'get_' . $row->Field;
			$methodName = ($input->getOption('camel')) ? Inflect::camelize($methodName) : Inflect::underscore($methodName);
			
			$primaryKey = ($row->Key == 'PRI') ? $methodName : $primaryKey;
			
			$accessor   = file_get_contents(__DIR__ . '/Templates/Doctrine/Miscellaneous/Accessor.txt');
			
			$search     = array('[field]', '[type]', '[method]');
			$replace    = array($row->Field, $type, $methodName);

			$accessors .= str_replace($search, $replace, $accessor) . "\n\n";

			/**
			 * The column to be displayed in the select() public method
			 */

			if (in_array($row->Field, $selectColumns)) {
				$model = str_replace('/* Column to be displayed in the dropdown */', $methodName . '()', $model);
			}

			/**
			 * Generate the mutators
			 */

			if ($row->Extra != 'auto_increment') {
				$class         = '\\' . ucfirst($name);
				$classVariable = NULL;
				
				$methodName = 'set_' . $row->Field;
				$methodName = ($input->getOption('camel')) ? Inflect::camelize($methodName) : Inflect::underscore($methodName);

				$nullable = ($row->Null == 'YES') ? ' = NULL' : NULL;

				$mutator = file_get_contents(__DIR__ . '/Templates/Doctrine/Miscellaneous/Mutator.txt');

				if ($row->Key == 'MUL') {
					$classVariable = '\\' . ucfirst(str_replace('_id', '', $row->Field)) . ' ';
				} elseif (in_array($this->_getDataType($row->Type), $dataTypes)) {
					$mutator = str_replace('$this->[field] = $[field];', '$this->[field] = new \DateTime($[field]);', $mutator);
				}

				$search  = array('[field]', '[type]', '[method]', '[classVariable]', '[class]', '[nullable]');
				$replace = array($row->Field, $type, $methodName, $classVariable, $class, $nullable);
				
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

		$filename = APPPATH . 'models/' . ucfirst($name) . '.php';

		if (file_exists($filename)) {
			$output->writeln('<error>The ' . $name . ' model already exists!</error>');
			
			exit();
		}

		$file = fopen($filename, 'wb');
		file_put_contents($filename, $model);

		$output->writeln('<info>The model "' . $name . '" has been created successfully!</info>');
	}

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
		$this->setName('create:model')
			->setDescription('Create a new model')
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
				'doctrine',
				null,
				InputOption::VALUE_NONE,
				'Create a new controller based on Doctrine'
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
		if ($input->getOption('doctrine')) {
			return $this->_doctrine($input, $output);
		}

		$accessors       = NULL;
		$columns         = NULL;
		$counter         = 0;
		$dataTypes       = array('time', 'date', 'datetime', 'datetimetz');
		$fields          = NULL;
		$fieldsCounter   = 0;
		$keywords        = NULL;
		$keywordsCounter = NULL;
		$mutators        = NULL;
		$mutatorsCounter = 0;
		$name            = Inflect::singularize($input->getArgument('name'));
		$primaryKey      = NULL;

		$selectColumns = array('name', 'description', 'label');

		$foreignKeys        = NULL;
		$foreignKeysCounter = 0;

		/**
		 * Get the model template
		 */
		
		$model = file_get_contents(__DIR__ . '/Templates/Model.txt');
		
		/**
		 * Get the columns from the specified name
		 */

		$databaseColumns = new GetColumns($input->getArgument('name'), $output);

		foreach ($databaseColumns->result() as $row) {
			$accessors .= ($counter != 0) ? '	' : NULL;
			$columns   .= ($counter != 0) ? '	' : NULL;
			$mutators  .= ($mutatorsCounter != 0) ? '	' : NULL;

			/**
			 * Generate keywords
			 */

			if ($row->Field != 'datetime_created' && $row->Field != 'datetime_updated' && $row->Field != 'password') {
				$keywords .= ($keywordsCounter != 0) ? '		' : NULL;
				$keywords .= '\'[firstLetter].' . $row->Field . '\'' . ",\n";

				$keywordsCounter++;
			}
			
			$columns .= 'protected $_' . $row->Field . ';' . "\n";

			/**
			 * Generate the accessors
			 */

			$methodName = 'get_' . $row->Field;
			$methodName = ($input->getOption('camel')) ? Inflect::camelize($methodName) : Inflect::underscore($methodName);
			
			$primaryKey = ($row->Key == 'PRI') ? $row->Field : $primaryKey;
			
			$accessor   = file_get_contents(__DIR__ . '/Templates/Miscellaneous/Accessor.txt');
			
			$search     = array('[field]', '[type]', '[method]');
			$replace    = array($row->Field, $row->Type, $methodName);

			$accessors .= str_replace($search, $replace, $accessor) . "\n\n";

			/**
			 * Generate fields
			 */

			$fields .= ($fieldsCounter != 0) ? ",\n" . '			' : NULL;
			$fields .= '\'' . $row->Field . '\' => $this->' . $methodName . '()';

			$fieldsCounter++;

			/**
			 * Generate foreign keys to the model
			 */

			if ($row->Key == 'MUL') {
				$foreignKeys .= ($foreignKeysCounter == 0) ? "\n		" : NULL;
				$foreignKeys .= ($foreignKeysCounter != 0) ? ",\n		" : NULL;
				$foreignKeys .= '\'' . $row->Field . '\' => \'' . str_replace('_id', '', $row->Field) . '\'';
				$foreignKeysCounter++;
			}

			/**
			 * Generate the mutators
			 */

			$class         = '\\' . ucfirst($name);
			$classVariable = NULL;
			
			$methodName = 'set_' . $row->Field;
			$methodName = ($input->getOption('camel')) ? Inflect::camelize($methodName) : Inflect::underscore($methodName);

			$nullable = ($row->Null == 'YES') ? ' = NULL' : NULL;

			$mutator = file_get_contents(__DIR__ . '/Templates/Miscellaneous/Mutator.txt');

			if ($row->Key == 'MUL') {
				$classVariable = '\\' . ucfirst(str_replace('_id', '', $row->Field)) . ' ';
			}

			$search  = array('[field]', '[type]', '[method]', '[classVariable]', '[nullable]');
			$replace = array($row->Field, $row->Type, $methodName, $classVariable, $nullable);
			
			$mutators .= str_replace($search, $replace, $mutator) . "\n\n";

			$mutatorsCounter++;

			$counter++;
		}

		$foreignKeys .= ($foreignKeysCounter != 0) ? "\n	" : NULL;

		/**
		 * Search and replace the following keywords from the template
		 */

		$search = array(
			'[className]',
			'[foreignKeys]',
			'[fields]',
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
			$class,
			$foreignKeys,
			$fields,
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