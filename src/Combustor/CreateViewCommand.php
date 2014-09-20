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
		
		$bootstrapFormControl = NULL;
		$bootstrapFormGroup = NULL;
		$bootstrapFormOpen = NULL;
		$bootstrapFormSubmit = ($input->getOption('bootstrap')) ? 'col-lg-12' : NULL;
		$bootstrapTable = NULL;

		if ($input->getOption('bootstrap')) {
			$bootstrapFormControl = 'form-control';
			$bootstrapFormGroup = 'form-group';
			$bootstrapFormOpen = 'form-horizontal';
			$bootstrapTable = 'table';
		}

		/**
		 * Get the view template
		 */
		
		$create = file_get_contents(__DIR__ . '/Templates/Views/Create.txt');
		$edit = file_get_contents(__DIR__ . '/Templates/Views/Edit.txt');
		$index = file_get_contents(__DIR__ . '/Templates/Views/Index.txt');
		$show = file_get_contents(__DIR__ . '/Templates/Views/Show.txt');

		/**
		 * Get the columns from the specified name
		 */

		$databaseColumns = new GetColumns($input->getArgument('name'), $output);

		$columns = NULL;
		$counter = 0;
		$fields = NULL;
		$formColumn = ($input->getOption('bootstrap')) ? 'col-lg-11' : NULL;
		$labelClass = ($input->getOption('bootstrap')) ? 'control-label col-lg-1' : NULL;
		$rows = NULL;
		$showFields = NULL;

		foreach ($databaseColumns->result() as $row) {
			/**
			 * Get the method name
			 */
			
			$methodName = 'get_' . $row->Field;
			$methodName = ($input->getOption('snake')) ? Inflect::underscore($methodName) : Inflect::camelize($methodName);

			if ($row->Key == 'PRI') {
				$primaryKey = $methodName;
			}

			if ($row->Field == 'datetime_created' || $row->Field == 'datetime_updated' || $row->Extra == 'auto_increment') {
				continue;
			}

			if ($counter != 0 && $row->Field != 'password') {
				$columns .= '				';
				$rows .= '					';
				$fields .= '		';
				$showFields .= '	';
			}

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

				$fields .= '			<?php echo form_label(\'' . Inflect::humanize($row->Field) . '\', \'' . $row->Field . '\', $attributes); ?>' . "\n";
				$fields .= '			<div class="$formColumn">' . "\n";
				$fields .= '				<?php echo form_input(\'' . $row->Field . '\', set_value(\'' . $row->Field . '\'), \'class="$bootstrapFormControl"\'); ?>' . "\n";
				$fields .= '				<?php echo form_error(\'' . $row->Field . '\'); ?>' . "\n";
				$fields .= '			</div>' . "\n";
				$fields .= '		</div>' . "\n";

				$showFields .= Inflect::humanize($row->Field) . ': <?php echo $$singular->' . $methodName . '(); ?>' . "\n";

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
			if ($row->Field == 'password') {
				$editFields .= file_get_contents(__DIR__ . '/Templates/Miscellaneous/EditPassword.txt') . "\n";
			}
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
			'$bootstrapFormSubmit',
			'$bootstrapTable',
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
			$bootstrapFormSubmit,
			$bootstrapTable,
			$labelClass,
			$formColumn,
			ucfirst(Inflect::pluralize($input->getArgument('name'))),
			ucfirst(Inflect::singularize($input->getArgument('name'))),
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