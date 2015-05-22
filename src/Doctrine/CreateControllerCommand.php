<?php namespace Rougin\Combustor\Doctrine;

use Describe\Describe;
use Combustor\Tools\Inflect;
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
	 */
	public function execute()
	{
		/**
		 * Set the name for the controller
		 */

		$name = ($this->_input->getOption('keep')) ? $this->_input->getArgument('name') : Inflect::pluralize($this->_input->getArgument('name'));

		/**
		 * Get the controller template
		 */

		$slash = (strpos(PHP_OS, 'WIN') !== FALSE) ? '\\' : '/';
		$doctrineDirectory = str_replace($slash . 'Doctrine', '', __DIR__);

		$controller = file_get_contents(__DIR__ . '/Templates/Controller.txt');

		/**
		 * Get the columns from the specified name
		 */

		require APPPATH . 'config/database.php';

		$db['default']['driver'] = $db['default']['dbdriver'];
		unset($db['default']['dbdriver']);

		$describe = new Describe($db['default']);
		$tableInformation = $describe->getInformationFromTable($this->_input->getArgument('name'));

		$models = '\'[singular]\'';

		$columnsOnCreate         = NULL;
		$columnsOnEdit           = NULL;
		$columnsToValidate       = NULL;
		$counter                 = 0;
		$dropdownColumn          = NULL;
		$dropdownColumnsOnCreate = '$data = array();';
		$dropdownColumnsOnEdit   = '$data[\'[singular]\'] = $this->doctrine->entity_manager->find(\'[singular]\', $id);';
		$selectColumns           = array('name', 'description', 'label');

		foreach ($tableInformation as $row) {
			$methodName = 'set_' . strtolower($row->field);
			$methodName = ($this->_input->getOption('camel')) ? Inflect::camelize($methodName) : Inflect::underscore($methodName);

			if ($counter != 0) {
				$columnsOnCreate   .= ($row->field != 'datetime_updated') ? '			' : NULL;
				$columnsOnEdit     .= ($row->field != 'datetime_created') ? '			' : NULL;

				if ($row->field != 'password' && $row->field != 'datetime_created' && $row->field != 'datetime_updated' && ! $row->isNull) {
					$columnsToValidate .= '			';
				}
			}

			if ($row->extra == 'auto_increment') {
				continue;
			} elseif ($row->key == 'MUL') {
				if ($row->key == 'MUL') {
					if (strpos($models, ",\n" . '			\'' . $row->referencedTable . '\'') === FALSE) {
						$models .= ",\n" . '			\'' . $row->referencedTable . '\'';
					}
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

				$dropdownColumn = '$data[\'' . Inflect::pluralize($row->referencedTable) . '\'] = $this->doctrine->get_all(\'' . $row->referencedTable . '\')->as_dropdown(\'' . $fieldDescription . '\');';

				$dropdownColumnsOnCreate .= "\n\t\t" . $dropdownColumn;
				$dropdownColumnsOnEdit   .= "\n\t\t" . $dropdownColumn;

				$columnsOnCreate .= '$' . $row->referencedTable . ' = $this->doctrine->entity_manager->find(\'' . $row->referencedTable . '\', $this->input->post(\'' . $row->field . '\'));' . "\n";
				$columnsOnCreate .= '			$this->[singular]->' . $methodName . '($' . $row->referencedTable . ');' . "\n\n";

				$columnsOnEdit .= '$' . $row->referencedTable . ' = $this->doctrine->entity_manager->find(\'' . $row->referencedTable . '\', $this->input->post(\'' . $row->field . '\'));' . "\n";
				$columnsOnEdit .= '			$[singular]->' . $methodName . '($' . $row->referencedTable . ');' . "\n\n";
			} elseif ($row->field == 'password') {
				$columnsOnCreate .= "\n" . file_get_contents($doctrineDirectory . '/Templates/Miscellaneous/CheckCreatePassword.txt') . "\n\n";
				$columnsOnEdit   .= "\n" . file_get_contents($doctrineDirectory . '/Templates/Miscellaneous/CheckEditPassword.txt') . "\n\n";

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
					$dropdownColumn = '$data[\'' . Inflect::pluralize($row->field) . '\'] = array(\'male\' => \'Male\', \'female\' => \'Female\');';

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

			if ($row->field != 'password' && $row->field != 'datetime_created' && $row->field != 'datetime_updated') {
				if ( ! $row->isNull) {
					$columnsToValidate .= '\'' . $row->field . '\' => \'' . strtolower(str_replace('_', ' ', $row->field)) . '\',' . "\n";
				}
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
			'[singularText]'
		);

		$plural     = ($this->_input->getOption('keep')) ? $name : Inflect::pluralize($name);
		$pluralText = ($this->_input->getOption('keep')) ? strtolower($name) : strtolower(Inflect::pluralize($name));

		$replace = array(
			rtrim($models),
			rtrim($dropdownColumnsOnCreate),
			rtrim($dropdownColumnsOnEdit),
			rtrim($columnsOnCreate),
			rtrim($columnsOnEdit),
			substr($columnsToValidate, 0, -2),
			ucfirst($name),
			ucfirst(str_replace('_', ' ', $name)),
			$plural,
			$pluralText,
			Inflect::singularize($name),
			strtolower(Inflect::humanize($name))
		);

		$controller = str_replace($search, $replace, $controller);

		/**
		 * Create a new file and insert the generated template
		 */

		$controllerFile = ($this->_input->getOption('lowercase')) ? strtolower($name) : ucfirst($name);

		$filename = APPPATH . 'controllers/' . $controllerFile . '.php';

		if (file_exists($filename)) {
			$this->_output->writeln('<error>The ' . $name . ' controller already exists!</error>');

			exit();
		}

		$file = fopen($filename, 'wb');
		file_put_contents($filename, $controller);

		$this->_output->writeln('<info>The controller "' . $name . '" has been created successfully!</info>');
	}

}