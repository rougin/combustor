<?php namespace Rougin\Combustor;

use Rougin\Combustor\Tools;
use Rougin\Describe\Describe;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateViewCommand extends Command
{

	/**
	 * Set the configurations of the specified command
	 */
	protected function configure()
	{
		$this->setName('create:view')
			->setDescription('Create a new view')
			->addArgument(
				'name',
				InputArgument::REQUIRED,
				'Name of the view folder'
			)->addOption(
				'bootstrap',
				NULL,
				InputOption::VALUE_NONE,
				'Include the Bootstrap CSS/JS Framework tags'
			)->addOption(
				'camel',
				NULL,
				InputOption::VALUE_NONE,
				'Use the camel case naming convention for the accessors and mutators'
			)->addOption(
				'keep',
				NULL,
				InputOption::VALUE_NONE,
				'Keeps the name to be used'
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
		if ( ! file_exists(APPPATH . 'views/layout')) {
			$message = 'Please create a layout first using the "create:layout" command!';
			exit($output->writeln('<error>' . $message . '</error>'));
		}

		/**
		 * Integrate Bootstrap if enabled
		 */

		$bootstrap = array(
			'button' => ($input->getOption('bootstrap')) ? 'btn btn-primary' : NULL,
			'buttonBack'   => ($input->getOption('bootstrap')) ? 'btn btn-default' : NULL,
			'buttonSubmit' => ($input->getOption('bootstrap')) ? 'btn btn-primary' : NULL,
			'formColumn'   => ($input->getOption('bootstrap')) ? '' : NULL,
			'formControl'  => ($input->getOption('bootstrap')) ? 'form-control' : NULL,
			'formGroup'    => ($input->getOption('bootstrap')) ? 'form-group col-lg-12 col-md-12 col-sm-12 col-xs-12' : NULL,
			'formOpen'     => ($input->getOption('bootstrap')) ? '' : NULL,
			'label'        => ($input->getOption('bootstrap')) ? 'control-label' : NULL,
			'table'        => ($input->getOption('bootstrap')) ? 'table table table-striped table-hover' : NULL
		);

		/**
		 * Get the view template
		 */

		$create = file_get_contents(__DIR__ . '/Templates/Views/Create.txt');
		$edit   = file_get_contents(__DIR__ . '/Templates/Views/Edit.txt');
		$index  = file_get_contents(__DIR__ . '/Templates/Views/Index.txt');
		$show   = file_get_contents(__DIR__ . '/Templates/Views/Show.txt');

		/**
		 * Get the columns from the specified name
		 */

		require APPPATH . 'config/database.php';

		$db['default']['driver'] = $db['default']['dbdriver'];
		unset($db['default']['dbdriver']);

		$describe = new Describe($db['default']);
		$tableInformation = $describe->getInformationFromTable($input->getArgument('name'));

		$columns        = NULL;
		$counter        = 0;
		$fieldsOnCreate = NULL;
		$fieldsOnShow   = NULL;
		$rows           = NULL;

		$dropdownColumnLabels = array('name', 'description', 'label');

		foreach ($tableInformation as $row) {
			$methodName = 'get_' . $row->field;
			$methodName = ($input->getOption('camel')) ? camelize($methodName) : underscore($methodName);

			$primaryKey = ($row->key == 'PRI') ? $methodName : $primaryKey;
			$required   = ( ! $row->isNull) ? ' required' : NULL;

			if ($row->field == 'datetime_created' || $row->field == 'datetime_updated' || $row->extra == 'auto_increment') {
				continue;
			}

			$columns        .= ($counter != 0 && $row->field != 'password') ? '					' : NULL;
			$fieldsOnCreate .= ($counter != 0 && $row->field != 'password') ? '		' : NULL;
			$fieldsOnShow   .= ($counter != 0 && $row->field != 'password') ? '	' : NULL;
			$rows           .= ($counter != 0 && $row->field != 'password') ? '						' : NULL;

			if ($row->field == 'password') {
				$fieldsOnCreate .= file_get_contents(__DIR__ . '/Templates/Miscellaneous/CreatePassword.txt') . "\n";
				continue;
			}

			$columns .= '<th>' . str_replace(' Id', '', humanize($row->field)) . '</th>' . "\n";

			$extension = NULL;
			if (strpos($row->field, 'date') !== FALSE || strpos($row->field, 'time') !== FALSE) {
				$extension = '->format(\'F d, Y\')';
			} else if ($row->key == 'MUL') {
				$tableColumns = $describe->getInformationFromTable($row->referencedTable);

				$tablePrimaryKey = NULL;
				foreach ($tableColumns as $column) {
					if (in_array($column->field, $dropdownColumnLabels) || $column->key == 'PRI') {
						$tablePrimaryKey = 'get_' . $column->field;

						if ($input->getOption('camel')) {
							$tablePrimaryKey = camelize($tablePrimaryKey);
						} else {
							$tablePrimaryKey = underscore($tablePrimaryKey);
						}
					}
				}

				$extension = '->' . $tablePrimaryKey . '()';
			}

			$rows .= '<td><?php echo $[singular]->' . $methodName . '()' . $extension . '; ?></td>' . "\n";

			if ($input->getOption('bootstrap')) {
				$fieldsOnCreate .= '<?php if (form_error(\'' . $row->field . '\')): ?>' . "\n";
				$fieldsOnCreate .= '			<div class="form-group has-error">' . "\n";
				$fieldsOnCreate .= '		<?php else: ?>' . "\n";
				$fieldsOnCreate .= '			<div class="form-group">' . "\n";
				$fieldsOnCreate .= '		<?php endif; ?>' . "\n";
			} else {
				$fieldsOnCreate .= '	<div class="">' . "\n";
			}

			$label = str_replace(' Id', '', humanize($row->field));
			$fieldsOnCreate .= '			<?php echo form_label(\'' . $label . '\', \'' . $row->field . '\', array(\'class\' => \'[bootstrapLabel]\')); ?>' . "\n";
			$fieldsOnCreate .= '			<div class="[bootstrapFormColumn]">' . "\n";

			if ($row->key == 'MUL') {
				$data = plural($row->referencedTable);

				$fieldsOnCreate .= '				<?php echo form_dropdown(\'' . $row->field . '\', $' . Tools::stripTableSchema($data) . ', set_value(\'' . $row->field . '\'), \'class="[bootstrapFormControl]"' . $required . '\'); ?>' . "\n";
				$tableColumns  = $describe->getInformationFromTable($row->referencedTable);

				$tablePrimaryKey = NULL;
				foreach ($tableColumns as $column) {
					if ($column->key == 'PRI') {
						$tablePrimaryKey = 'get_' . $column->field;

						if ($input->getOption('camel')) {
							$tablePrimaryKey = camelize($tablePrimaryKey);
						} else {
							$tablePrimaryKey = underscore($tablePrimaryKey);
						}
					}
				}

				$value = '$[singular]->' . $methodName . '()->' . $tablePrimaryKey . '()';
			} else if ($row->field == 'gender') {
				$fieldsOnCreate .= '				<?php echo form_dropdown(\'' . $row->field . '\', $' . plural($row->field) .', set_value(\'' . $row->field . '\'), \'class="[bootstrapFormControl]"' . $required . '\'); ?>' . "\n";
			} else {
				$fieldsOnCreate .= '				<?php echo form_input(\'' . $row->field . '\', set_value(\'' . $row->field . '\'), \'class="[bootstrapFormControl]"' . $required . '\'); ?>' . "\n";

				$value = '$[singular]->' . $methodName . '()';
			}

			$fieldsOnCreate .= '				<?php echo form_error(\'' . $row->field . '\'); ?>' . "\n";
			$fieldsOnCreate .= '			</div>' . "\n";
			$fieldsOnCreate .= '		</div>' . "\n";

			if (strpos($row->type, 'date') !== FALSE || strpos($row->type, 'time') !== FALSE) {
				$format = NULL;

				switch ($row->type) {
					case 'datetime':
						$format = 'Y-m-d H:i:s';
						break;
					case 'date':
						$format = 'Y-m-d';
						break;
					case 'time':
						$format = 'H:i:s';
						break;
				}

				$value = '$[singular]->' . $methodName . '()->format(\'' . $format . '\')';
			}

			$fieldsOnShow .= str_replace(' Id', '', humanize($row->field)) . ': <?php echo ' . $value . '; ?><br>' . "\n";

			$counter++;
		}

		/**
		 * Generate form for edit.php
		 */

		$fieldsOnEdit = $fieldsOnCreate;

		foreach ($tableInformation as $row) {
			$methodName = 'get_' . $row->field;
			$methodName = ($input->getOption('camel')) ? camelize($methodName) : underscore($methodName);

			$value = '$[singular]->' . $methodName . '()';

			if ($row->key == 'MUL') {
				$tableColumns = $describe->getInformationFromTable($row->referencedTable);

				$tablePrimaryKey = NULL;
				foreach ($tableColumns as $column) {
					if ($column->key == 'PRI') {
						$tablePrimaryKey = 'get_' . $column->field;

						if ($input->getOption('camel')) {
							$tablePrimaryKey = camelize($tablePrimaryKey);
						} else {
							$tablePrimaryKey = underscore($tablePrimaryKey);
						}
					}
				}

				$value = '$[singular]->' . $methodName . '()->' . $tablePrimaryKey . '()';
			} else if (strpos($row->type, 'date') !== FALSE || strpos($row->type, 'time') !== FALSE) {
				$format = NULL;

				switch ($row->type) {
					case 'datetime':
						$format = 'Y-m-d H:i:s';
						break;
					case 'date':
						$format = 'Y-m-d';
						break;
					case 'time':
						$format = 'H:i:s';
						break;
				}

				$value = '$[singular]->' . $methodName . '()->format(\'' . $format . '\')';
			}

			if (strpos($fieldsOnEdit, 'set_value(\'' . $row->field . '\')') !== FALSE) {
				$replace = 'set_value(\'' . $row->field . '\', ' . $value . ')';
				$search  = 'set_value(\'' . $row->field . '\')';

				$fieldsOnEdit = str_replace($search, $replace, $fieldsOnEdit);
			}

			if ($row->field == 'password') {
				$createPassword = file_get_contents(__DIR__ . '/Templates/Miscellaneous/CreatePassword.txt') . "\n";
				$createPassword = str_replace('set_value(\'password\')', 'set_value(\'password\', $[singular]->' . $methodName . '())', $createPassword);

				$fieldsOnEdit  = str_replace($createPassword, '', $fieldsOnEdit);
				$fieldsOnEdit .= file_get_contents(__DIR__ . '/Templates/Miscellaneous/EditPassword.txt') . "\n";
			}
		}

		$columns .= '					<th></th>' . "\n";

		$search = array(
			'[fieldsOnShow]',
			'[fieldsOnEdit]',
			'[fieldsOnCreate]',
			'[columns]',
			'[rows]',
			'[primaryKey]',
			'[bootstrapButton]',
			'[bootstrapButtonBack]',
			'[bootstrapButtonSubmit]',
			'[bootstrapFormControl]',
			'[bootstrapFormGroup]',
			'[bootstrapFormOpen]',
			'[bootstrapTable]',
			'[bootstrapLabel]',
			'[bootstrapFormColumn]',
			'[entity]',
			'[singular]',
			'[plural]',
			'[singularEntity]',
			'[pluralEntity]'
		);

		$plural = ($input->getOption('keep')) ? $input->getArgument('name') : plural($input->getArgument('name'));

		$replace = array(
			rtrim($fieldsOnShow),
			rtrim($fieldsOnEdit),
			rtrim($fieldsOnCreate),
			rtrim($columns),
			rtrim($rows),
			$primaryKey,
			$bootstrap['button'],
			$bootstrap['buttonBack'],
			$bootstrap['buttonSubmit'],
			$bootstrap['formControl'],
			$bootstrap['formGroup'],
			$bootstrap['formOpen'],
			$bootstrap['table'],
			$bootstrap['label'],
			$bootstrap['formColumn'],
			ucwords(Tools::stripTableSchema(str_replace('_', ' ', plural($input->getArgument('name'))))),
			Tools::stripTableSchema(singular($input->getArgument('name'))),
			Tools::stripTableSchema($plural),
			ucwords(Tools::stripTableSchema(str_replace('_', ' ', singular($input->getArgument('name'))))),
			Tools::stripTableSchema(str_replace('_', ' ', plural($input->getArgument('name'))))
		);

		$create = str_replace($search, $replace, $create);
		$edit   = str_replace($search, $replace, $edit);
		$index  = str_replace($search, $replace, $index);
		$show   = str_replace($search, $replace, $show);

		/**
		 * Create the directory first
		 */

		$viewDirectory = plural($input->getArgument('name'));

		if ($input->getOption('keep')) {
			$viewDirectory = $input->getArgument('name');
		}

		$viewDirectory = Tools::stripTableSchema($viewDirectory);
		$filepath = APPPATH . 'views/' . $viewDirectory . '/';

		if ( ! @mkdir($filepath, 0777, TRUE)) {
			$message = 'The ' . plural($input->getArgument('name')) . ' views folder already exists!';
			$output->writeln('<error>' . $message . '</error>');
		} else {
			/**
			 * Create the files
			 */

			$createFile = fopen($filepath . 'create.php', 'wb');
			$editFile   = fopen($filepath . 'edit.php', 'wb');
			$indexFile  = fopen($filepath . 'index.php', 'wb');
			$showFile   = fopen($filepath . 'show.php', 'wb');

			file_put_contents($filepath . 'create.php', $create);
			file_put_contents($filepath . 'edit.php', $edit);
			file_put_contents($filepath . 'index.php', $index);
			file_put_contents($filepath . 'show.php', $show);

			$message = 'The views folder "' . plural($input->getArgument('name')) . '" has been created successfully!';
			$output->writeln('<info>' . $message . '</info>');
		}
	}

}