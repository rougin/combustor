<?php namespace Combustor\Doctrine;

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
		$this->setName('doctrine:controller')
			->setDescription('Create a new Doctrine-based controller')
			->addArgument(
				'name',
				InputArgument::REQUIRED,
				'Name of the controller'
			)->addOption(
				'keep',
				NULL,
				InputOption::VALUE_NONE,
				'Keeps the name to be used'
			)->addOption(
				'lowercase',
				NULL,
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

		$slash = (strpos(PHP_OS, 'WIN') !== FALSE) ? '\\' : '/';
		$doctrineDirectory = str_replace($slash . 'Doctrine', '', __DIR__);

		$controller = file_get_contents($doctrineDirectory . '/Templates/Doctrine/Controller.txt');

		/**
		 * Get the columns from the specified name
		 */

		require APPPATH . 'config/database.php';

		$db['default']['driver'] = $db['default']['dbdriver'];
		unset($db['default']['dbdriver']);

		$describe = new Describe($db['default']);
		$tableInformation = $describe->getInformationFromTable($input->getArgument('name'));

		$models = '\'[singular]\'';

		$columnsOnCreate         = NULL;
		$columnsOnEdit           = NULL;
		$columnsToValidate       = NULL;
		$counter                 = 0;
		$dropdownColumn          = NULL;
		$dropdownColumnsOnCreate = '$data = array();';
		$dropdownColumnsOnEdit   = '$data[\'[singular]\'] = $this->doctrine->em->find(\'[singular]\', $id);';

		foreach ($tableInformation as $row) {
			if ($row->key == 'PRI') {
				$primaryKey = $row->field;
			}

			$methodName = 'set_' . strtolower($row->field);
			$methodName = ($input->getOption('camel')) ? Inflect::camelize($methodName) : Inflect::underscore($methodName);

			if ($counter != 0) {
				$columnsOnCreate   .= ($row->field != 'datetime_updated') ? '			' : NULL;
				$columnsOnEdit     .= ($row->field != 'datetime_created') ? '			' : NULL;
				$columnsToValidate .= ($row->field != 'password' && $row->field != 'datetime_created' && $row->field != 'datetime_updated' &&  ! $row->isNull) ? '			' : NULL;
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

				foreach ($foreignTableInformation as $foreignRow) {
					if ($foreignRow->key == 'MUL') {
						if (strpos($models, ",\n" . '			\'' . $foreignRow->referencedTable . '\'') === FALSE) {
							$models .= ",\n" . '			\'' . $foreignRow->referencedTable . '\'';
						}
					}
				}

				$dropdownColumn = '$data[\'' . Inflect::pluralize($row->referencedTable) . '\'] = $this->' . $row->referencedTable . '->select();';

				$dropdownColumnsOnCreate .= "\n\t\t" . $dropdownColumn;
				$dropdownColumnsOnEdit   .= "\n\t\t" . $dropdownColumn;

				$columnsOnCreate .= '$' . $row->referencedTable . ' = $this->doctrine->em->find(\'' . $row->referencedTable . '\', $this->input->post(\'' . $row->field . '\'));' . "\n";
				$columnsOnCreate .= '			$this->[singular]->' . $methodName . '($' . $row->referencedTable . ');' . "\n\n";

				$columnsOnEdit .= '$' . $row->referencedTable . ' = $this->doctrine->em->find(\'' . $row->referencedTable . '\', $this->input->post(\'' . $row->field . '\'));' . "\n";
				$columnsOnEdit .= '			$[singular]->' . $methodName . '($' . $row->referencedTable . ');' . "\n\n";
			} elseif ($row->field == 'password') {
				$columnsOnCreate .= "\n" . file_get_contents($doctrineDirectory . '/Templates/Miscellaneous/CheckCreatePassword.txt') . "\n\n";
				$columnsOnEdit   .= "\n" . file_get_contents($doctrineDirectory . '/Templates/Miscellaneous/CheckEditPassword.txt') . "\n\n";

				$columnsOnCreate = str_replace('[method]', $methodName, $columnsOnCreate);
				$columnsOnEdit   = str_replace('[method]', $methodName, $columnsOnEdit);
			} elseif ($row->field == 'gender') {
				$dropdownColumn = '$data[\'' . Inflect::pluralize($row->field) . '\'] = array(\'male\' => \'Male\', \'female\' => \'Female\');';

				$dropdownColumnsOnCreate .= "\n\t\t" . $dropdownColumn;
				$dropdownColumnsOnEdit   .= "\n\t\t" . $dropdownColumn;
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
			'[primaryKey]',
			'[plural]',
			'[pluralText]',
			'[singular]',
			'[singularText]'
		);

		$plural     = ($input->getOption('keep')) ? $name : Inflect::pluralize($name);
		$pluralText = ($input->getOption('keep')) ? strtolower($name) : strtolower(Inflect::pluralize($name));

		$replace = array(
			rtrim($models),
			rtrim($dropdownColumnsOnCreate),
			rtrim($dropdownColumnsOnEdit),
			rtrim($columnsOnCreate),
			rtrim($columnsOnEdit),
			substr($columnsToValidate, 0, -2),
			ucfirst($name),
			ucfirst(str_replace('_', ' ', $name)),
			$primaryKey,
			$plural,
			$pluralText,
			Inflect::singularize($name),
			strtolower(Inflect::humanize($name))
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