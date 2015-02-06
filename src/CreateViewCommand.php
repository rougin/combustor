<?php namespace Combustor;

use Combustor\Tools\Describe;
use Combustor\Tools\Inflect;
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
		 * Integrate Bootstrap if enabled
		 */

		$bootstrapButton           = ($input->getOption('bootstrap')) ? 'btn btn-primary btn-lg' : NULL;		
		$bootstrapFormControl      = ($input->getOption('bootstrap')) ? 'form-control' : NULL;
		$bootstrapFormGroup        = ($input->getOption('bootstrap')) ? 'form-group' : NULL;
		$bootstrapFormOpen         = ($input->getOption('bootstrap')) ? 'form-horizontal' : NULL;
		$bootstrapFormSubmit       = ($input->getOption('bootstrap')) ? 'col-lg-12' : NULL;
		$bootstrapTable            = ($input->getOption('bootstrap')) ? 'table table table-striped table-hover' : NULL;

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

		$databaseColumns = new Describe($input->getArgument('name'), $output);

		$columns    = NULL;
		$counter    = 0;
		$fields     = NULL;
		$formColumn = ($input->getOption('bootstrap')) ? 'col-lg-11' : NULL;
		$labelClass = ($input->getOption('bootstrap')) ? 'control-label col-lg-1' : NULL;
		$pullRight  = ($input->getOption('bootstrap')) ? 'pull-right' : NULL;
		$rows       = NULL;
		$showFields = NULL;

		$selectColumns = array('name', 'description', 'label');

		foreach ($databaseColumns->result() as $row) {
			$methodName = 'get_' . $row->field;
			$methodName = ($input->getOption('camel')) ? Inflect::camelize($methodName) : Inflect::underscore($methodName);

			$primaryKey = ($row->key == 'PRI') ? $methodName : $primaryKey;

			if ($row->field == 'datetime_created' || $row->field == 'datetime_updated' || $row->extra == 'auto_increment') continue;

			$columns    .= ($counter != 0 && $row->field != 'password') ? '				' : NULL;
			$rows       .= ($counter != 0 && $row->field != 'password') ? '					' : NULL;
			$fields     .= ($counter != 0 && $row->field != 'password') ? '		' : NULL;
			$showFields .= ($counter != 0 && $row->field != 'password') ? '	' : NULL;

			if ($row->field != 'password') {
				$columns .= '<th>' . str_replace(' Id', '', Inflect::humanize($row->field)) . '</th>' . "\n";

				if (strpos($row->field, 'date') !== FALSE || strpos($row->field, 'time') !== FALSE) {
					$extend = '->format(\'F d, Y\')';
				} elseif ($row->key == 'MUL') {
					$tableColumns = new Describe($row->referenced_table, $output);

					$tablePrimaryKey = NULL;
					foreach ($tableColumns->result() as $column) {
						if (in_array($column->field, $selectColumns) || $column->key == 'PRI') {
							$tablePrimaryKey = 'get_' . $column->field;

							if ($input->getOption('camel')) {
								$tablePrimaryKey = Inflect::camelize($tablePrimaryKey);
							} else {
								$tablePrimaryKey = Inflect::underscore($tablePrimaryKey);
							}
						}
					}

					$extend = '->' . $tablePrimaryKey . '()';
				} else {
					$extend = NULL;
				}

				$rows .= '<td><?php echo $[singular]->' . $methodName . '()' . $extend . '; ?></td>' . "\n";

				if ($input->getOption('bootstrap')) {
					$fields .= '<?php if (form_error(\'' . $row->field . '\')): ?>' . "\n";
					$fields .= '			<div class="form-group has-error">' . "\n";
					$fields .= '		<?php else: ?>' . "\n";
					$fields .= '			<div class="form-group">' . "\n";
					$fields .= '		<?php endif; ?>' . "\n";
				} else {
					$fields .= '	<div class="[bootstrapFormGroup]">' . "\n";
				}

				$fields .= '			<?php echo form_label(\'' . str_replace(' Id', '', Inflect::humanize($row->field)) . '\', \'' . $row->field . '\', array(\'class\' => \'[labelClass]\')); ?>' . "\n";
				$fields .= '			<div class="[formColumn]">' . "\n";
				
				if ($row->key == 'MUL') {
					$data    = Inflect::pluralize($row->referenced_table);
					$fields .= '				<?php echo form_dropdown(\'' . $row->field . '\', $' . $data . ', set_value(\'' . $row->field . '\'), \'class="[bootstrapFormControl]"\'); ?>' . "\n";
				} else {
					$fields .= '				<?php echo form_input(\'' . $row->field . '\', set_value(\'' . $row->field . '\'), \'class="[bootstrapFormControl]"\'); ?>' . "\n";
				}

				$fields .= '				<?php echo form_error(\'' . $row->field . '\'); ?>' . "\n";
				$fields .= '			</div>' . "\n";
				$fields .= '		</div>' . "\n";

				if ($row->key == 'MUL') {
					$tableColumns = new Describe($row->referenced_table, $output);

					$tablePrimaryKey = NULL;
					foreach ($tableColumns->result() as $column) {
						if ($column->key == 'PRI') {
							$tablePrimaryKey = 'get_' . $column->field;
							
							if ($input->getOption('camel')) {
								$tablePrimaryKey = Inflect::camelize($tablePrimaryKey);
							} else {
								$tablePrimaryKey = Inflect::underscore($tablePrimaryKey);
							}
						}
					}

					$value = '$[singular]->' . $methodName . '()->' . $tablePrimaryKey . '()';
				} else {
					$value = '$[singular]->' . $methodName . '()';
				}

				$showFields .= str_replace(' Id', '', Inflect::humanize($row->field)) . ': <?php echo ' . $value . '; ?><br>' . "\n";
			} else {
				$fields .= file_get_contents(__DIR__ . '/Templates/Miscellaneous/CreatePassword.txt') . "\n";
			}

			$counter++;
		}

		/**
		 * Generate form for edit.php
		 */

		$editFields = $fields;

		foreach ($databaseColumns->result() as $row) {
			$methodName = 'get_' . $row->field;
			$methodName = ($input->getOption('camel')) ? Inflect::camelize($methodName) : Inflect::underscore($methodName);

			if ($row->key == 'MUL') {
				$tableColumns = new Describe($row->referenced_table, $output);

				$tablePrimaryKey = NULL;
				foreach ($tableColumns->result() as $column) {
					if ($column->key == 'PRI') {
						$tablePrimaryKey = 'get_' . $column->field;
						
						if ($input->getOption('camel')) {
							$tablePrimaryKey = Inflect::camelize($tablePrimaryKey);
						} else {
							$tablePrimaryKey = Inflect::underscore($tablePrimaryKey);
						}
					}
				}
				
				$value = '$[singular]->' . $methodName . '()->' . $tablePrimaryKey . '()';
			} else {
				$value = '$[singular]->' . $methodName . '()';
			}

			if (strpos($editFields, 'set_value(\'' . $row->field . '\')') !== FALSE) {
				$editFields = str_replace('set_value(\'' . $row->field . '\')', 'set_value(\'' . $row->field . '\', ' . $value . ')', $editFields);
			}

			$createPassword  = file_get_contents(__DIR__ . '/Templates/Miscellaneous/CreatePassword.txt') . "\n";
			
			$editFields .= ($row->field == 'password') ? file_get_contents(__DIR__ . '/Templates/Miscellaneous/EditPassword.txt') . "\n" : NULL;
			$editFields  = str_replace($createPassword, '', $editFields);
		}

		$columns .= '				<th></th>' . "\n";

		$search = array(
			'[showFields]',
			'[editFields]',
			'[fields]',
			'[columns]',
			'[rows]',
			'[primaryKey]',
			'[bootstrapButton]',
			'[bootstrapFormControl]',
			'[bootstrapFormGroup]',
			'[bootstrapFormOpen]',
			'[bootstrapFormSubmit]',
			'[bootstrapTable]',
			'[pullRight]',
			'[labelClass]',
			'[formColumn]',
			'[entity]',
			'[singularEntity]',
			'[plural]',
			'[singular]'
		);

		$replace = array(
			rtrim($showFields),
			rtrim($editFields),
			rtrim($fields),
			rtrim($columns),
			rtrim($rows),
			$primaryKey,
			$bootstrapButton,
			$bootstrapFormControl,
			$bootstrapFormGroup,
			$bootstrapFormOpen,
			$bootstrapFormSubmit,
			$bootstrapTable,
			$pullRight,
			$labelClass,
			$formColumn,
			ucwords(str_replace('_', ' ', Inflect::pluralize($input->getArgument('name')))),
			ucwords(str_replace('_', ' ', Inflect::singularize($input->getArgument('name')))),
			Inflect::pluralize($input->getArgument('name')),
			Inflect::singularize($input->getArgument('name'))
		);

		$create = str_replace($search, $replace, $create);
		$edit   = str_replace($search, $replace, $edit);
		$index  = str_replace($search, $replace, $index);
		$show   = str_replace($search, $replace, $show);

		/**
		 * Create the directory first
		 */

		$filepath = APPPATH . 'views/' . Inflect::pluralize($input->getArgument('name')) . '/';

		if ( ! @mkdir($filepath, 0777, true)) {
			$output->writeln('<error>The ' . Inflect::pluralize($input->getArgument('name')) . ' views folder already exists!</error>');

			exit();
		}

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

		$output->writeln('<info>The views folder "' . Inflect::pluralize($input->getArgument('name')) . '" has been created successfully!</info>');
	}

}