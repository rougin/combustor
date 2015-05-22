<?php namespace Rougin\Combustor\Wildfire;

use Describe\Describe;
use Combustor\Tools\Inflect;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateModelCommand
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
		$fields          = NULL;
		$fieldsCounter   = 0;
		$keywords        = NULL;
		$keywordsCounter = NULL;
		$mutators        = NULL;
		$mutatorsCounter = 0;
		$name            = Inflect::singularize($this->_input->getArgument('name'));
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

		require APPPATH . 'config/database.php';

		$db['default']['driver'] = $db['default']['dbdriver'];
		unset($db['default']['dbdriver']);

		$describe = new Describe($db['default']);
		$tableInformation = $describe->getInformationFromTable($this->_input->getArgument('name'));

		foreach ($tableInformation as $row) {
			$accessors .= ($counter != 0) ? '	' : NULL;
			$columns   .= ($counter != 0) ? '	' : NULL;
			$mutators  .= ($mutatorsCounter != 0) ? '	' : NULL;
			$type       = ($row->key == 'MUL') ? '\\' . ucwords($row->referencedTable) : $row->type;

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
			
			$primaryKey = ($row->key == 'PRI') ? $row->field : $primaryKey;
			
			$accessor = file_get_contents(__DIR__ . '/Templates/Miscellaneous/Accessor.txt');
			
			$dataType = $row->type;

			if (strpos($row->type, '(') !== FALSE) {
				$leftParenthesis = strpos($row->type, '(');
				$dataType = substr($row->type, 0, $leftParenthesis);
			}

			if (in_array($dataType, $dataTypes)) {
				$dateFormat  = 'if (is_a($this->_[field], \'DateTime\'))' . "\n";
				$dateFormat .= '		{' . "\n";
				$dateFormat .= '			return $this->_[field];' . "\n";
				$dateFormat .= '		}' . "\n\n";
				$dateFormat .= '		return new DateTime($this->_[field]);';

				$accessor = str_replace('return $this->_[field];', $dateFormat, $accessor);
				$type = 'varchar()';
			}

			$search  = array('[field]', '[type]', '[method]');
			$replace = array($row->field, $type, $methodName);

			$accessors .= str_replace($search, $replace, $accessor) . "\n\n";

			/**
			 * Generate fields
			 */

			$fields .= ($fieldsCounter != 0) ? ",\n" . '			' : NULL;
			$fields .= '\'' . $row->field . '\' => $this->' . $methodName . '()';

			if ($row->key == 'MUL') {
				$foreignTableInformation = $describe->getInformationFromTable($row->referencedTable);

				foreach ($foreignTableInformation as $foreignRow) {
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
			$classVariable = ($row->key == 'MUL') ? '\\' . ucfirst($row->referencedTable) . ' ' : NULL;
			
			$methodName = 'set_' . $row->field;

			$nullable = ($row->isNull) ? ' = NULL' : NULL;

			$mutator = file_get_contents(__DIR__ . '/Templates/Miscellaneous/Mutator.txt');

			if (in_array($dataType, $dataTypes)) {
				$mutator = str_replace('$this->_[field] = $[field]', '$this->_[field] = new DateTime($[field])', $mutator);
			}

			$search  = array('[field]', '[type]', '[method]', '[classVariable]', '[nullable]');
			$replace = array($row->field, $type, $methodName, $classVariable, $nullable);

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
			'[model]',
			'[modelName]'
		);

		$replace = array(
			$class,
			$fields,
			rtrim($columns),
			rtrim(substr($keywords, 0, -2)),
			rtrim($accessors),
			rtrim($mutators),
			$primaryKey,
			Inflect::pluralize($this->_input->getArgument('name')),
			$name,
			substr($this->_input->getArgument('name'), 0, 1),
			ucfirst($name),
			ucwords(str_replace('_', ' ', $name))
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

}