<?php namespace Combustor;

use Describe\Describe;
use Combustor\Tools\Inflect;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateControllerCommand extends Command
{

	/**
	 * Set the configurations of the specified command
	 */
	protected function configure()
	{
		$this->setName('create:controller')
			->setDescription('Create a new controller')
			->addArgument(
				'name',
				InputArgument::REQUIRED,
				'Name of the controller'
			)->addOption(
				'keep',
				null,
				InputOption::VALUE_NONE,
				'Keeps the name to be used'
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

		/**
		 * Set the name for the controller
		 */

		$name = ($input->getOption('keep')) ? $input->getArgument('name') : Inflect::pluralize($input->getArgument('name'));

		/**
		 * Get the controller template
		 */
		
		$controller = file_get_contents(__DIR__ . '/Templates/Controller.txt');
		
		/**
		 * Get the columns from the specified name
		 */

		require APPPATH . 'config/database.php';

		$db['default']['driver'] = ($db['default']['dbdriver'] == 'mysqli') ? 'mysql' : $db['default']['dbdriver'];

		$describe = new Describe($db['default']);
		$tableInformation = $describe->getInformationFromTable($input->getArgument('name'));

		$models = '\'[singular]\'';

		$columnsOnCreate         = NULL;
		$columnsOnCreateCounter  = 0;
		$columnsOnEdit           = NULL;
		$columnsToValidate       = NULL;
		$counter                 = 0;
		$dropdownColumnsOnCreate = '$data = array();';
		$dropdownColumnsOnEdit   = '$data[\'[singular]\'] = $this->factory->find(\'[singular]\', array(\'[primaryKey]\' => $id));';
		$dropdowns               = 0;
		$selectColumns           = array('name', 'description', 'label');
		$singularText            = strtolower(Inflect::humanize($input->getArgument('name')));

		foreach ($tableInformation as $row) {
			if ($row->key == 'PRI') {
				$primaryKey = $row->field;
			}

			$methodName = 'set_' . strtolower($row->field);
			$methodName = ($input->getOption('camel')) ? Inflect::camelize($methodName) : Inflect::underscore($methodName);

			if ($counter != 0) {
				$columnsOnCreate   .= ($row->field != 'datetime_updated') ? '			' : NULL;
				$columnsOnEdit     .= ($row->field != 'datetime_created') ? '			' : NULL;
				$columnsToValidate .= ($row->field != 'password' && $row->field != 'datetime_created' && $row->field != 'datetime_updated') ? '			' : NULL;
			}

			if ($row->extra == 'auto_increment') {
				continue;
			} elseif ($row->key == 'MUL') {
				if (strpos($models, ",\n" . '			\'' . $row->referencedTable . '\'') === FALSE) {
					$models .= ",\n" . '			\'' . $row->referencedTable . '\'';
				}

				$foreignTableInformation = $describe->getInformationFromTable($row->referencedTable);

				$fieldDescription = $foreignTableInformation->getPrimaryKey();
				foreach ($foreignTable->result() as $foreignRow) {
					if ($foreignRow->key == 'MUL') {
						if (strpos($models, ",\n" . '			\'' . $foreignRow->referencedTable . '\'') === FALSE) {
							$models .= ",\n" . '			\'' . $foreignRow->referencedTable . '\'';
						}
					}

					$fieldDescription = in_array($foreignRow->field, $selectColumns) ? $foreignRow->field : $fieldDescription;
				}

				$dropdownColumn = '$data[\'' . Inflect::pluralize($row->referencedTable) . '\'] = $this->factory->get_all(\'' . $row->referencedTable . '\')->as_dropdown(\'' . $fieldDescription . '\');';

				$dropdownColumnsOnCreate .= "\n\t\t" . $dropdownColumn;
				$dropdownColumnsOnEdit   .= "\n\t\t" . $dropdownColumn;

				$columnsOnCreate .= '$' . $row->referencedTable . ' = $this->factory->find(\'' . $row->referencedTable . '\', array(\'' . $row->referencedColumn . '\' => $this->input->post(\'' . $row->field . '\')));' . "\n";
				$columnsOnCreate .= '			$this->[singular]->' . $methodName . '($' . $row->referencedTable . ');' . "\n\n";

				$columnsOnEdit .= '$' . $row->referencedTable . ' = $this->factory->find(\'' . $row->referencedTable . '\', array(\'' . $row->referencedColumn . '\' => $this->input->post(\'' . $row->field . '\')));' . "\n";
				$columnsOnEdit .= '			$[singular]->' . $methodName . '($' . $row->referencedTable . ');' . "\n\n";
			} elseif ($row->field == 'password') {
				$columnsOnCreate .= "\n" . file_get_contents(__DIR__ . '/Templates/Miscellaneous/CheckCreatePassword.txt') . "\n\n";
				$columnsOnEdit   .= "\n" . file_get_contents(__DIR__ . '/Templates/Miscellaneous/CheckEditPassword.txt') . "\n\n";

				$columnsOnCreate = str_replace('[method]', $methodName, $columnsOnCreate);
				$columnsOnEdit   = str_replace('[method]', $methodName, $columnsOnEdit);
			} else {
				if ($row->field == 'datetime_created' || $row->field == 'datetime_updated') {
					$column = '\'now\'';
				} else {
					$column = '$this->input->post(\'' . $row->field . '\')';
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
					$columnsToValidate .= '\'' . $row->field . '\' => \'' . ucwords(str_replace('_', ' ', $row->field)) . '\',' . "\n";
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
			'[primaryKey]',
			'[plural]',
			'[singular]',
			'[singularText]'
		);

		$replace = array(
			rtrim($models),
			rtrim($dropdownColumnsOnCreate),
			rtrim($dropdownColumnsOnEdit),
			rtrim($columnsOnCreate),
			rtrim($columnsOnEdit),
			substr($columnsToValidate, 0, -2),
			ucfirst(Inflect::pluralize($input->getArgument('name'))),
			ucfirst(str_replace('_', ' ', Inflect::pluralize($input->getArgument('name')))),
			$primaryKey,
			Inflect::pluralize($input->getArgument('name')),
			Inflect::singularize($input->getArgument('name')),
			$singularText
		);

		$controller = str_replace($search, $replace, $controller);

		/**
		 * Create a new file and insert the generated template
		 */

		$controllerFile = ($input->getOption('lowercase')) ? strtolower($name) : ucfirst($name);

		$filename = APPPATH . 'controllers/' . $controllerFile . '.php';

		if (file_exists($filename)) {
			$output->writeln('<error>The ' . $name . ' controller already exists!</error>');

			exit();
		}

		$file = fopen($filename, 'wb');
		file_put_contents($filename, $controller);

		$output->writeln('<info>The controller "' . $name . '" has been created successfully!</info>');
	}
	
}