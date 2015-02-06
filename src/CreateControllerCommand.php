<?php namespace Combustor;

use Combustor\Tools\Describe;
use Combustor\Tools\Inflect;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
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

		$columns = new Describe($input->getArgument('name'), $output);

		$models = '\'[singular]\'';

		$columnsCreate   = NULL;
		$columnsCreateCounter = 0;
		$columnsEdit     = NULL;
		$columnsValidate = NULL;
		$counter         = 0;
		$dropdownColumns = NULL;
		$dropdowns       = 0;
		$selectColumns   = array('name', 'description', 'label');
		$singularText    = strtolower(Inflect::humanize($input->getArgument('name')));

		foreach ($columns->result() as $row) {
			if ($row->key == 'PRI') {
				$primaryKey = $row->field;
			}

			$methodName = 'set_' . strtolower($row->field);
			$methodName = ($input->getOption('camel')) ? Inflect::camelize($methodName) : Inflect::underscore($methodName);

			if ($counter != 0) {
				$columnsCreate   .= ($row->field != 'datetime_updated') ? '			' : NULL;
				$columnsEdit     .= ($row->field != 'datetime_created') ? '			' : NULL;
				$columnsValidate .= ($row->field != 'password' && $row->field != 'datetime_created' && $row->field != 'datetime_updated') ? '			' : NULL;
			}

			$dropdownColumns .= ($dropdowns != 0) ? '		' : NULL;

			if ($row->extra == 'auto_increment') {
				continue;
			} elseif ($row->key == 'MUL') {
				$entity = str_replace('_id', '', $row->field);

				if (strpos($models, ",\n" . '			\'' . $row->referenced_table . '\'') === FALSE) {
					$models .= ",\n" . '			\'' . $row->referenced_table . '\'';
				}

				$foreignTable = new Describe($row->referenced_table, $output);

				$fieldDescription = $row->field;
				foreach ($foreignTable->result() as $foreignRow) {
					$fieldDescription = in_array($foreignRow->field, $selectColumns) ? $foreignRow->field : $fieldDescription;
				}

				$dropdownColumns .= '$data[\'' . Inflect::pluralize($entity) . '\'] = $this->factory->get_all(\'' . $row->referenced_table . '\')->as_dropdown(\'' . $fieldDescription . '\');' . "\n";

				$columnsCreate .= '$' . $row->referenced_table . ' = $this->factory->find(\'' . $row->referenced_table . '\', array(\'' . $row->referenced_column . '\' => $this->input->post(\'' . $row->field . '\')));' . "\n";
				$columnsCreate .= '			$this->[singular]->' . $methodName . '($' . $row->referenced_table . ');' . "\n\n";

				$columnsEdit .= '$' . $row->referenced_table . ' = $this->factory->find(\'' . $row->referenced_table . '\', array(\'' . $row->field . '\' => $this->input->post(\'' . $row->referenced_column . '\')));' . "\n";
				$columnsEdit .= '			$[singular]->' . $methodName . '($' . $row->referenced_table . ');' . "\n\n";

				$dropdowns++;
			} elseif ($row->field == 'password') {
				$columnsCreate .= "\n" . file_get_contents(__DIR__ . '/Templates/Miscellaneous/CheckCreatePassword.txt') . "\n\n";
				$columnsEdit   .= "\n" . file_get_contents(__DIR__ . '/Templates/Miscellaneous/CheckEditPassword.txt') . "\n\n";

				$columnsCreate = str_replace('[method]', $methodName, $columnsCreate);
				$columnsEdit   = str_replace('[method]', $methodName, $columnsEdit);
			} else {
				$column = ($row->field == 'datetime_created' || $row->field == 'datetime_updated') ? 'now' : '$this->input->post(\'' . $row->field . '\')';

				if ($row->field != 'datetime_updated') {
					$columnsCreate .= '$this->[singular]->' . $methodName . '(' . $column . ');' . "\n";
				}

				if ($row->field != 'datetime_created') {
					$columnsEdit .= '$[singular]->' . $methodName . '(' . $column . ');' . "\n";
				}
			}

			if ($row->field != 'password' && $row->field != 'datetime_created' && $row->field != 'datetime_updated') {
				$columnsValidate .= '\'' . $row->field . '\' => \'' . str_replace('_', ' ', $row->field) . '\',' . "\n";
			}

			$counter++;
		}

		/**
		 * Search and replace the following keywords from the template
		 */

		$search = array(
			'[models]',
			'[primaryKey]',
			'[dropdownColumns]',
			'[columnsCreate]',
			'[columnsEdit]',
			'[columnsValidate]',
			'[controller]',
			'[controllerName]',
			'[plural]',
			'[singular]',
			'[singularText]'
		);

		$replace = array(
			rtrim($models),
			$primaryKey,
			rtrim($dropdownColumns),
			rtrim($columnsCreate),
			rtrim($columnsEdit),
			substr($columnsValidate, 0, -2),
			ucfirst(Inflect::pluralize($input->getArgument('name'))),
			ucfirst(str_replace('_', ' ', Inflect::pluralize($input->getArgument('name')))),
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