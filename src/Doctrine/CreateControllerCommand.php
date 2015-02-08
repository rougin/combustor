<?php namespace Combustor\Doctrine;

use Combustor\Tools\Describe;
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
		$this->setName('doctrine:controller')
			->setDescription('Create a new Doctrine-based controller')
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
		
		$doctrineDirectory = str_replace('/Doctrine', '', __DIR__);
		$controller = file_get_contents($doctrineDirectory . '/Templates/Doctrine/Controller.txt');

		/**
		 * Get the columns from the specified name
		 */

		$columns = new Describe($input->getArgument('name'), $output);

		$models = '\'[singular]\'';

		$columnsOnCreate         = NULL;
		$columnsOnEdit           = NULL;
		$columnsToValidate       = NULL;
		$counter                 = 0;
		$dropdownColumnsOnCreate = '$data = array();';
		$dropdownColumnsOnEdit   = '$data[\'[singular]\'] = $this->doctrine->em->find(\'[singular]\', $id);';
		$dropdowns               = 0;
		$singularText            = strtolower(Inflect::humanize($input->getArgument('name')));

		foreach ($columns->result() as $row) {
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

			if ($dropdowns != 0) {
				$dropdownColumnsOnCreate .= '		';
				$dropdownColumnsOnEdit   .= '		';
			}

			if ($row->extra == 'auto_increment') {
				continue;
			} elseif ($row->key == 'MUL') {
				$entity  = str_replace('_id', '', $row->field);
				$models .= ",\n" . '			\'' . $entity . '\'';

				$dropdownColumns .= '$data[\'' . Inflect::pluralize($entity) . '\'] = $this->' . $entity . '->select();' . "\n";

				$dropdownColumnsOnCreate .= $dropdownColumn;
				$dropdownColumnsOnEdit   .= $dropdownColumn;

				$columnsOnCreate .= '$' . $entity . ' = $this->doctrine->em->find(\'' . $entity . '\', $this->input->post(\'' . $row->field . '\'));' . "\n";
				$columnsOnCreate .= '			$this->[singular]->' . $methodName . '($' . $entity . ');' . "\n\n";

				$columnsOnEdit .= '$' . $entity . ' = $this->doctrine->em->find(\'' . $entity . '\', $this->input->post(\'' . $row->field . '\'));' . "\n";
				$columnsOnEdit .= '			$[singular]->' . $methodName . '($' . $entity . ');' . "\n\n";

				$dropdowns++;
			} elseif ($row->field == 'password') {
				$columnsOnCreate .= "\n" . file_get_contents($doctrineDirectory . '/Templates/Miscellaneous/CheckCreatePassword.txt') . "\n\n";
				$columnsOnEdit   .= "\n" . file_get_contents($doctrineDirectory . '/Templates/Miscellaneous/CheckEditPassword.txt') . "\n\n";

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
				$columnsToValidate .= '\'' . $row->field . '\' => \'' . str_replace('_', ' ', $row->field) . '\',' . "\n";
			}

			$counter++;
		}

		/**
		 * Search and replace the following keywords from the template
		 */

		$search = array(
			'[models]',
			'[primaryKey]',
			'[dropdownColumnsOnCreate]',
			'[dropdownColumnsOnEdit]',
			'[columnsOnCreate]',
			'[columnsOnEdit]',
			'[columnsToValidate]',
			'[controller]',
			'[controllerName]',
			'[plural]',
			'[singular]',
			'[singularText]'
		);

		$replace = array(
			rtrim($models),
			$primaryKey,
			rtrim($dropdownColumnsOnCreate),
			rtrim($dropdownColumnsOnEdit),
			rtrim($columnsOnCreate),
			rtrim($columnsOnEdit),
			substr($columnsToValidate, 0, -2),
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