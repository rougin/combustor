<?php namespace Combustor;

use Combustor\Tools\GetColumns;
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

		$columns = new GetColumns($input->getArgument('name'), $output);

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
			if ($row->Key == 'PRI') {
				$primaryKey = $row->Field;
			}

			$methodName = 'set_' . strtolower($row->Field);
			$methodName = ($input->getOption('camel')) ? Inflect::camelize($methodName) : Inflect::underscore($methodName);

			if ($counter != 0) {
				$columnsCreate   .= ($row->Field != 'datetime_updated') ? '			' : NULL;
				$columnsEdit     .= ($row->Field != 'datetime_created') ? '			' : NULL;
				$columnsValidate .= ($row->Field != 'password' && $row->Field != 'datetime_created' && $row->Field != 'datetime_updated') ? '			' : NULL;
			}

			$dropdownColumns .= ($dropdowns != 0) ? '		' : NULL;

			if ($row->Extra == 'auto_increment') {
				continue;
			} elseif ($row->Key == 'MUL') {
				$entity = str_replace('_id', '', $row->Field);

				if (strpos($models, ",\n" . '			\'' . $row->Referenced_Table . '\'') === FALSE) {
					$models .= ",\n" . '			\'' . $row->Referenced_Table . '\'';
				}

				$fieldDescription = $row->Field;
				$fieldDescription = in_array($row->Field, $selectColumns) ? $row->Field : $fieldDescription;

				$dropdownColumns .= '$data[\'' . Inflect::pluralize($entity) . '\'] = $this->factory->get_all(\'' . $row->Referenced_Table . '\')->as_dropdown(\'' . $row->Referenced_Table . '\', \'' . $row->Referenced_Column . '\');' . "\n";

				$columnsCreate .= '$' . $row->Referenced_Table . ' = $this->factory->find(\'' . $row->Referenced_Table . '\', array(\'' . $row->Referenced_Column . '\' => $this->input->post(\'' . $row->Referenced_Column . '\')));' . "\n";
				$columnsCreate .= '			$this->[singular]->' . $methodName . '($' . $row->Referenced_Table . ');' . "\n\n";

				$columnsEdit .= '$' . $row->Referenced_Table . ' = $this->factory->find(\'' . $row->Referenced_Table . '\', array(\'' . $row->Field . '\' => $this->input->post(\'' . $row->Referenced_Column . '\')));' . "\n";
				$columnsEdit .= '			$[singular]->' . $methodName . '($' . $row->Referenced_Table . ');' . "\n\n";

				$dropdowns++;
			} elseif ($row->Field == 'password') {
				$columnsCreate .= "\n" . file_get_contents(__DIR__ . '/Templates/Miscellaneous/CheckCreatePassword.txt') . "\n\n";
				$columnsEdit   .= "\n" . file_get_contents(__DIR__ . '/Templates/Miscellaneous/CheckEditPassword.txt') . "\n\n";

				$columnsCreate = str_replace('[method]', $methodName, $columnsCreate);
				$columnsEdit   = str_replace('[method]', $methodName, $columnsEdit);
			} else {
				$column = ($row->Field == 'datetime_created' || $row->Field == 'datetime_updated') ? 'date(\'Y-m-d H:i:s\')' : '$this->input->post(\'' . $row->Field . '\')';

				if ($row->Field != 'datetime_updated') {
					$columnsCreate .= '$this->[singular]->' . $methodName . '(' . $column . ');' . "\n";
				}

				if ($row->Field != 'datetime_created') {
					$columnsEdit .= '$[singular]->' . $methodName . '(' . $column . ');' . "\n";
				}
			}

			if ($row->Field != 'password' && $row->Field != 'datetime_created' && $row->Field != 'datetime_updated') {
				$columnsValidate .= '\'' . $row->Field . '\' => \'' . str_replace('_', ' ', $row->Field) . '\',' . "\n";
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