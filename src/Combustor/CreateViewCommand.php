<?php

namespace Combustor;

use Combustor\Tools\Inflect;
use Combustor\Tools\GetColumns;
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
				'snake',
				NULL,
				InputOption::VALUE_NONE,
				'Use the snake case naming convention for the accessor and mutators'
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
		
		$bootstrapFormControl      = ($input->getOption('bootstrap')) ? 'form-control' : NULL;
		$bootstrapFormGroup        = ($input->getOption('bootstrap')) ? 'form-group' : NULL;
		$bootstrapFormOpen         = ($input->getOption('bootstrap')) ? 'form-horizontal' : NULL;
		$bootstrapFormSubmit       = ($input->getOption('bootstrap')) ? 'col-lg-12' : NULL;
		$bootstrapFormSubmitButton = ($input->getOption('bootstrap')) ? 'btn btn-default' : NULL;
		$bootstrapTable            = ($input->getOption('bootstrap')) ? 'table' : NULL;

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

		$databaseColumns = new GetColumns($input->getArgument('name'), $output);

		$columns    = NULL;
		$counter    = 0;
		$fields     = NULL;
		$formColumn = ($input->getOption('bootstrap')) ? 'col-lg-11' : NULL;
		$labelClass = ($input->getOption('bootstrap')) ? 'control-label col-lg-1' : NULL;
		$pullRight  = ($input->getOption('bootstrap')) ? 'pull-right' : NULL;
		$rows       = NULL;
		$showFields = NULL;

		foreach ($databaseColumns->result() as $row) {
			$methodName = 'get_' . $row->Field;
			$methodName = ($input->getOption('snake')) ? Inflect::underscore($methodName) : Inflect::camelize($methodName);

			$primaryKey = ($row->Key == 'PRI') ? $methodName : NULL;

			if ($row->Field == 'datetime_created' || $row->Field == 'datetime_updated' || $row->Extra == 'auto_increment') continue;

			$columns    .= ($counter != 0 && $row->Field != 'password') ? '				' : NULL;
			$rows       .= ($counter != 0 && $row->Field != 'password') ? '					' : NULL;
			$fields     .= ($counter != 0 && $row->Field != 'password') ? '		' : NULL;
			$showFields .= ($counter != 0 && $row->Field != 'password') ? '	' : NULL;

			if ($row->Field != 'password') {
				$columns .= '<th>' . Inflect::humanize($row->Field) . '</th>' . "\n";

				if (strpos($row->Field, 'date') !== FALSE) {
					$extend = '->format(\'F d, Y\')';
				} elseif ($row->Key == 'MUL') {
					$extend = '->' . $methodName . '()';
				} else {
					$extend = NULL;
				}

				$rows .= '<td><?php echo $$singular->' . $methodName . '()' . $extend . '; ?></td>' . "\n";

				if ($input->getOption('bootstrap')) {
					$fields .= '<?php if (form_error(\'' . $row->Field . '\')): ?>' . "\n";
					$fields .= '			<div class="form-group has-error">' . "\n";
					$fields .= '		<?php else: ?>' . "\n";
					$fields .= '			<div class="form-group">' . "\n";
					$fields .= '		<?php endif; ?>' . "\n";
				} else {
					$fields .= '	<div class="$bootstrapFormGroup">' . "\n";
				}

				$fields .= '			<?php echo form_label(\'' . str_replace(' Id', '', Inflect::humanize($row->Field)) . '\', \'' . $row->Field . '\', array(\'class\' => \'$labelClass\')); ?>' . "\n";
				$fields .= '			<div class="$formColumn">' . "\n";
				
				if ($row->Key == 'MUL') {
					$data    = Inflect::pluralize(str_replace('_id', '', $row->Field));
					$fields .= '				<?php echo form_dropdown(\'' . $row->Field . '\', $' . $data . ', set_value(\'' . $row->Field . '\'), \'class="$bootstrapFormControl"\'); ?>' . "\n";
				} else {
					$fields .= '				<?php echo form_input(\'' . $row->Field . '\', set_value(\'' . $row->Field . '\'), \'class="$bootstrapFormControl"\'); ?>' . "\n";
				}

				$fields .= '				<?php echo form_error(\'' . $row->Field . '\'); ?>' . "\n";
				$fields .= '			</div>' . "\n";
				$fields .= '		</div>' . "\n";

				$showFields .= Inflect::humanize($row->Field) . ': <?php echo $$singular->' . $methodName . '(); ?><br>' . "\n";
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
			$methodName = 'get_' . $row->Field;
			$methodName = ($input->getOption('snake')) ? Inflect::underscore($methodName) : Inflect::camelize($methodName);

			if (strpos($editFields, 'set_value(\'' . $row->Field . '\')') !== FALSE) {
				$editFields = str_replace('set_value(\'' . $row->Field . '\')', 'set_value(\'' . $row->Field . '\', $$singular->' . $methodName . '())', $editFields);
			}

			$editFields .= ($row->Field == 'password') ? file_get_contents(__DIR__ . '/Templates/Miscellaneous/EditPassword.txt') . "\n" : NULL;
		}

		$columns .= '				<th></th>' . "\n";

		$search = array(
			'$showFields',
			'$editFields',
			'$fields',
			'$columns',
			'$rows',
			'$primaryKey',
			'$bootstrapFormControl',
			'$bootstrapFormGroup',
			'$bootstrapFormOpen',
			'$bootstrapFormSubmitButton',
			'$bootstrapFormSubmit',
			'$bootstrapTable',
			'$pullRight',
			'$labelClass',
			'$formColumn',
			'$entity',
			'$singularEntity',
			'$plural',
			'$singular'
		);

		$replace = array(
			rtrim($showFields),
			rtrim($editFields),
			rtrim($fields),
			rtrim($columns),
			rtrim($rows),
			$primaryKey,
			$bootstrapFormControl,
			$bootstrapFormGroup,
			$bootstrapFormOpen,
			$bootstrapFormSubmitButton,
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
		$edit = str_replace($search, $replace, $edit);
		$index = str_replace($search, $replace, $index);
		$show = str_replace($search, $replace, $show);

		/**
		 * Create the directory first
		 */

		$filepath = APPPATH . 'views/' . Inflect::pluralize($input->getArgument('name')) . '/';

		if ( ! @mkdir($filepath, 0777, true)) {
			$output->writeln('<error>The ' . Inflect::pluralize($input->getArgument('name')) . ' controller already exists!</error>');

			exit();
		}

		/**
		 * Create the files
		 */

		$create_file = fopen($filepath . 'create.php', 'wb');
		$edit_file = fopen($filepath . 'edit.php', 'wb');
		$index_file = fopen($filepath . 'index.php', 'wb');
		$show_file = fopen($filepath . 'show.php', 'wb');

		file_put_contents($filepath . 'create.php', $create);
		file_put_contents($filepath . 'edit.php', $edit);
		file_put_contents($filepath . 'index.php', $index);
		file_put_contents($filepath . 'show.php', $show);

		$output->writeln('<info>The views folder "' . Inflect::pluralize($input->getArgument('name')) . '" has been created successfully!</info>');
	}
	
}