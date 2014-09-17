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

		if ( ! $databaseColumns->result()) {
			$output->writeln('<error>There is no table named "' . $input->getArgument('name') . '" from the database!</error>');

			exit();
		}

		$columns = NULL;
		$counter = 0;
		$fields = NULL;
		$rows = NULL;

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

			if ($counter != 0) {
				$columns .= '				';
				$rows .= '					';
				$fields .= '	';
			}

			if ($row->Field != 'password') {
				$separator = (strpos($row->Field, '_') !== FALSE) ? '_' : ' ';
				$column = ucwords(preg_replace('/[' . $separator . ']+/', ' ', trim(strtolower($row->Field))));

				$columns .= '<th>' . $column . '</th>' . "\n";

				$rows .= '<td><?php echo $$singular->' . $methodName . '(); ?><td>' . "\n";

				if ($input->getOption('bootstrap')) {
					$fields .= '<?php if (form_error(\'' . $row->Field . '\')): ?>' . "\n";
					$fields .= '		<div class="form-group has-error">' . "\n";
					$fields .= '	<?php else: ?>' . "\n";
					$fields .= '		<div class="form-group">' . "\n";
					$fields .= '	<?php endif; ?>' . "\n";
				} else {
					$fields .= '<div class="$bootstrapFormGroup">' . "\n";
				}

				$fields .= '		<?php echo form_input(\'' . $row->Field . '\', set_value(\'' . $row->Field . '\'), \'class="$bootstrapFormControl"\'); ?>' . "\n";
				$fields .= '		<?php echo form_error(\'' . $row->Field . '\'); ?>' . "\n";
				$fields .= '	</div>' . "\n";
			} else {
				$fields .= '<div class="$bootstrapFormGroup">' . "\n";
				$fields .= '		<label for="confirm_password">Password</label>' . "\n";
				$fields .= '		<?php echo form_password(\'password\', set_value(\'confirm_password\')); ?>' . "\n";
				$fields .= '	</div>' . "\n";
				$fields .= '	<div class="$bootstrapFormGroup">' . "\n";
				$fields .= '		<label for="confirm_password">Confirm Password</label>' . "\n";
				$fields .= '		<?php echo form_password(\'confirm_password\', set_value(\'confirm_password\')); ?>' . "\n";
				$fields .= '	</div>' . "\n";
			}

			$counter++;
		}

		$editFields = $fields;

		foreach ($databaseColumns->result() as $row) {
			/**
			 * Get the method name
			 */

			$methodName = 'get_' . $row->Field;
			$methodName = ($input->getOption('snake')) ? Inflect::underscore($methodName) : Inflect::camelize($methodName);

			if (strpos($editFields, 'set_value(\'' . $row->Field . '\')') !== FALSE) {
				$editFields = str_replace('set_value(\'' . $row->Field . '\')', 'set_value(\'' . $row->Field . '\', $$singular->' . $methodName . '())', $editFields);
			}
		}

		$search = array(
			'$editFields',
			'$fields',
			'$columns',
			'$rows',
			'$primaryKey',
			'$bootstrapFormControl',
			'$bootstrapFormGroup',
			'$bootstrapFormOpen',
			'$bootstrapTable',
			'$entity',
			'$plural',
			'$singular'
		);

		$replace = array(
			rtrim($editFields),
			rtrim($fields),
			rtrim($columns),
			rtrim($rows),
			$primaryKey,
			$bootstrapFormControl,
			$bootstrapFormGroup,
			$bootstrapFormOpen,
			$bootstrapTable,
			ucfirst(Inflect::pluralize($input->getArgument('name'))),
			Inflect::pluralize($input->getArgument('name')),
			Inflect::singularize($input->getArgument('name'))
		);

		$create = str_replace($search, $replace, $create);
		$edit = str_replace($search, $replace, $edit);
		$index = str_replace($search, $replace, $index);
		$show = str_replace($search, $replace, $show);

		echo $edit;
	}
	
}