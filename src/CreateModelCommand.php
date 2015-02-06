<?php namespace Combustor;

use Combustor\Tools\Describe;
use Combustor\Tools\Inflect;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateModelCommand extends Command
{

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

		$foreignKeys        = NULL;
		$foreignKeysCounter = 0;

		/**
		 * Get the model template
		 */
		
		$model = file_get_contents(__DIR__ . '/Templates/Model.txt');
		
		/**
		 * Get the columns from the specified name
		 */

		$databaseColumns = new Describe($input->getArgument('name'), $output);

		foreach ($databaseColumns->result() as $row) {
			$accessors .= ($counter != 0) ? '	' : NULL;
			$columns   .= ($counter != 0) ? '	' : NULL;
			$mutators  .= ($mutatorsCounter != 0) ? '	' : NULL;

			/**
			 * Generate keywords
			 */

			if ($row->field != 'datetime_created' && $row->field != 'datetime_updated' && $row->field != 'password') {
				$keywords .= ($keywordsCounter != 0) ? '		' : NULL;
				$keywords .= '\'[firstLetter].' . $row->field . '\'' . ",\n";

				$keywordsCounter++;
			}
			
			$columns .= 'protected $_' . $row->field . ';' . "\n";

			/**
			 * Generate the accessors
			 */

			$methodName = 'get_' . $row->field;
			$methodName = ($input->getOption('camel')) ? Inflect::camelize($methodName) : Inflect::underscore($methodName);
			
			$primaryKey = ($row->key == 'PRI') ? $row->field : $primaryKey;
			
			$accessor = file_get_contents(__DIR__ . '/Templates/Miscellaneous/Accessor.txt');
			
			$dataType = $row->type;

			if (strpos($row->type, '(') !== FALSE) {
				$leftParenthesis = strpos($row->type, '(');
				$dataType = substr($row->type, 0, $leftParenthesis);
			}

			if (in_array($dataType, $dataTypes)) {
				$dateFormat = 'if (is_a($this->_[field], \'DateTime\')) {
			return $this->_[field];
		}

		return new DateTime($this->_[field]);';
				$accessor = str_replace('return $this->_[field];', $dateFormat, $accessor);
			}

			$search  = array('[field]', '[type]', '[method]');
			$replace = array($row->field, $row->type, $methodName);

			$accessors .= str_replace($search, $replace, $accessor) . "\n\n";

			/**
			 * Generate fields
			 */

			$fields .= ($fieldsCounter != 0) ? ",\n" . '			' : NULL;
			$fields .= '\'' . $row->field . '\' => $this->' . $methodName . '()';

			if ($row->key == 'MUL') {
				$foreignTable = new Describe($row->referenced_table, $output);

				foreach ($foreignTable->result() as $foreignRow) {
					if ($foreignRow->key == 'PRI') {
						$methodName = 'get_' . $foreignRow->field;
						break;
					}
				}

				$fields .= '->' . $methodName . '()';
			} else if (in_array($dataType, $dataTypes)) {
				switch ($dataType) {
					case 'time':
						$format = '\'H:i:s\'';
						break;
					case 'date':
						$format = '\'Y-m-d\'';
						break;
					default:
						$format = '\'Y-m-d H:i:s\'';
						break;
				}

				$fields .= '->format(' . $format . ')';
			}

			$fieldsCounter++;

			/**
			 * Generate the mutators
			 */

			$class         = '\\' . ucfirst($name);
			$classVariable = NULL;
			
			$methodName = 'set_' . $row->field;
			$methodName = ($input->getOption('camel')) ? Inflect::camelize($methodName) : Inflect::underscore($methodName);

			$nullable = ($row->null == 'YES') ? ' = NULL' : NULL;

			$mutator = file_get_contents(__DIR__ . '/Templates/Miscellaneous/Mutator.txt');

			if ($row->key == 'MUL') {
				$classVariable = '\\' . ucfirst(str_replace('_id', '', $row->field)) . ' ';
			}

			if (in_array($dataType, $dataTypes)) {
				$mutator = str_replace('$this->_[field] = $[field]', '$this->_[field] = new DateTime($[field])', $mutator);
			}

			$search  = array('[field]', '[type]', '[method]', '[classVariable]', '[nullable]');
			$replace = array($row->field, $row->type, $methodName, $classVariable, $nullable);
			
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