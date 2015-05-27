<?php namespace Rougin\Combustor\Wildfire;

use Rougin\Combustor\Tools;
use Rougin\Describe\Describe;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateControllerCommand
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
	 *
	 * @return int
	 */
	public function execute()
	{
		/**
		 * Set the name for the controller
		 */

		if ($this->_input->getOption('keep')) {
			$name = $this->_input->getArgument('name');
		} else {
			$name = plural($this->_input->getArgument('name'));
		}

		$plural = ($this->_input->getOption('keep')) ? $name : plural($name);

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
		$tableInformation = $describe->getInformationFromTable($this->_input->getArgument('name'));

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

		foreach ($tableInformation as $row) {
			if ($row->extra == 'auto_increment') {
				continue;
			}

			$methodName = 'set_' . strtolower($row->field);
			$methodName = ($this->_input->getOption('camel')) ? camelize($methodName) : underscore($methodName);

			if ($counter != 0) {
				$columnsOnCreate   .= ($row->field != 'datetime_updated' && $row->key != 'MUL') ? '			' : NULL;
				$columnsOnEdit     .= ($row->field != 'datetime_created' && $row->key != 'MUL') ? '			' : NULL;

				if ($row->field != 'password' && $row->field != 'datetime_created' && $row->field != 'datetime_updated' && ! $row->isNull) {
					$columnsToValidate .= '			';
				}

				if ($tableInformation[$counter + 1]->key == 'MUL' && $tableInformation[$counter]->key != 'MUL') {
					$columnsOnCreate .= "\n";
					$columnsOnEdit   .= "\n";
				}
			}

			if ($row->key == 'MUL') {
				if (strpos($models, ",\n" . '			\'' . $row->referencedTable . '\'') === FALSE) {
					$models .= ",\n" . '			\'' . $row->referencedTable . '\'';
				}

				$foreignTableInformation = $describe->getInformationFromTable($row->referencedTable);
				$fieldDescription = $describe->getPrimaryKey($row->referencedTable);

				foreach ($foreignTableInformation as $foreignRow) {
					if ($foreignRow->key == 'MUL') {
						if (strpos($models, ",\n" . '			\'' . $foreignRow->referencedTable . '\'') === FALSE) {
							$models .= ",\n" . '			\'' . $foreignRow->referencedTable . '\'';
						}
					}

					$fieldDescription = in_array($foreignRow->field, $selectColumns) ? $foreignRow->field : $fieldDescription;
				}

				$dropdownColumn = '$data[\'' . plural(Tools::stripTableSchema($row->referencedTable)) . '\'] = $this->wildfire->get_all(\'' . $row->referencedTable . '\')->as_dropdown(\'' . $fieldDescription . '\');';

				$dropdownColumnsOnCreate .= "\n\t\t" . $dropdownColumn;
				$dropdownColumnsOnEdit   .= "\n\t\t" . $dropdownColumn;

				if ($counter != 0) {
					$columnsOnCreate .= "\t\t\t";
					$columnsOnEdit   .= "\t\t\t";
				}

				$columnsOnCreate .= '$' . $row->referencedTable . ' = $this->wildfire->find(\'' . $row->referencedTable . '\', $this->input->post(\'' . $row->field . '\'));' . "\n";
				$columnsOnCreate .= '			$this->[singular]->' . $methodName . '($' . $row->referencedTable . ');' . "\n\n";

				$columnsOnEdit .= '$' . $row->referencedTable . ' = $this->wildfire->find(\'' . $row->referencedTable . '\', $this->input->post(\'' . $row->field . '\'));' . "\n";
				$columnsOnEdit .= '			$[singular]->' . $methodName . '($' . $row->referencedTable . ');' . "\n\n";
			} else if ($row->field == 'password') {
				$columnsOnCreate .= "\n" . file_get_contents(__DIR__ . '/../Templates/Miscellaneous/CheckCreatePassword.txt') . "\n\n";
				$columnsOnEdit   .= "\n" . file_get_contents(__DIR__ . '/../Templates/Miscellaneous/CheckEditPassword.txt') . "\n\n";

				$getMethodName = str_replace('set', 'get', $methodName);

				$columnsOnCreate = str_replace('[method]', $methodName, $columnsOnCreate);
				$columnsOnEdit   = str_replace(array('[method]', '[getMethod]'), array($methodName, $getMethodName), $columnsOnEdit);
			} else {
				if ($row->field == 'datetime_created' || $row->field == 'datetime_updated') {
					$column = '\'now\'';
				} else {
					$column = '$this->input->post(\'' . $row->field . '\')';
				}

				if ($row->field == 'gender') {
					$dropdownColumn = '$data[\'' . plural(Tools::stripTableSchema($row->field)) . '\'] = array(\'male\' => \'Male\', \'female\' => \'Female\');';

					$dropdownColumnsOnCreate .= "\n\t\t" . $dropdownColumn;
					$dropdownColumnsOnEdit   .= "\n\t\t" . $dropdownColumn;
				}

				if ($row->field != 'datetime_updated') {
					$columnsOnCreate .= '$this->[singular]->' . $methodName . '(' . $column . ');' . "\n";
				}

				if ($row->field != 'datetime_created') {
					$columnsOnEdit .= '$[singular]->' . $methodName . '(' . $column . ');' . "\n";
				}
			}

			if (! $row->isNull && $row->field != 'password' && $row->field != 'datetime_created' && $row->field != 'datetime_updated') {
				$columnsToValidate .= '\'' . $row->field . '\' => \'' . strtolower(str_replace('_', ' ', $row->field)) . '\',' . "\n";
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
			Tools::stripTableSchema($plural),
			strtolower($plural),
			Tools::stripTableSchema(singular($name)),
			strtolower(humanize($name)),
		);

		$controller = str_replace($search, $replace, $controller);

		/**
		 * Create a new file and insert the generated template
		 */

		$controllerFile = ($this->_input->getOption('lowercase')) ? strtolower($name) : ucfirst($name);
		$filename = APPPATH . 'controllers/' . $controllerFile . '.php';

		if (file_exists($filename)) {
			$this->_output->writeln('<error>The ' . $name . ' controller already exists!</error>');
		} else {
			$file = fopen($filename, 'wb');
			file_put_contents($filename, $controller);

			$this->_output->writeln('<info>The controller "' . $name . '" has been created successfully!</info>');
		}
	}

}