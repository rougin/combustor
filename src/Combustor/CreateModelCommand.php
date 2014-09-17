<?php
namespace Combustor;

use Combustor\Tools\Inflect;
use Combustor\Tools\GetColumns;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateModelCommand extends Command
{

	/**
	 * Set the configurations of the specified command
	 */
	protected function configure()
	{
		$this->setName('create:model')
			->setDescription('Create a new model')
			->addArgument(
				'name',
				InputArgument::REQUIRED,
				'Name of the model'
			)->addOption(
				'snake',
				NULL,
				InputOption::VALUE_NONE,
				'Use the snake case naming convention for the accessor and mutators'
			);
	}

	/**
	 * Get the data type of the specified column
	 * 
	 * @param  string $type
	 * @return string
	 */
	protected function getDataType($type)
	{
		if (strpos($type, 'array') !== FALSE) $type = 'array';
		elseif (strpos($type, 'bigint') !== FALSE) $type = 'bigint';
		elseif (strpos($type, 'blob') !== FALSE) $type = 'blob';
		elseif (strpos($type, 'boolean') !== FALSE) $type = 'boolean';
		elseif (strpos($type, 'datetime') !== FALSE || strpos($type, 'timestamp') !== FALSE) $type = 'datetime';
		elseif (strpos($type, 'datetimetz') !== FALSE) $type = 'datetimetz';
		elseif (strpos($type, 'date') !== FALSE) $type = 'date';
		elseif (strpos($type, 'decimal') !== FALSE) $type = 'decimal';
		elseif (strpos($type, 'float') !== FALSE) $type = 'float';
		elseif (strpos($type, 'guid') !== FALSE) $type = 'guid';
		elseif (strpos($type, 'int') !== FALSE) $type = 'integer';
		elseif (strpos($type, 'json_array') !== FALSE) $type = 'json_array';
		elseif (strpos($type, 'object') !== FALSE) $type = 'object';
		elseif (strpos($type, 'simple_array') !== FALSE) $type = 'simple_array';
		elseif (strpos($type, 'smallint') !== FALSE) $type = 'smallint';
		elseif (strpos($type, 'text') !== FALSE) $type = 'text';
		elseif (strpos($type, 'time') !== FALSE) $type = 'time';
		elseif (strpos($type, 'varchar') !== FALSE) $type = 'string';

		return $type;
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
		 * Get the model template
		 */
		
		$model = file_get_contents(__DIR__ . '/Templates/Model.txt');
		
		/**
		 * Get the columns from the specified name
		 */

		$databaseColumns = new GetColumns($input->getArgument('name'), $output);

		$accessors = NULL;
		$columns = NULL;
		$counter = 0;
		$indexCounter = 0;
		$mutatorCounter = 0;
		$indexes = NULL;
		$mutators = NULL;

		foreach ($databaseColumns->result() as $row) {
			$nullable = ($row->Null == 'YES') ? 'TRUE' : 'FALSE';

			$type = $this->getDataType($row->Type);

			/**
			 * The data type is an integer or string? Set the length of the specified data type
			 */

			if ((strpos($row->Type, 'int') !== FALSE) || (strpos($row->Type, 'varchar') !== FALSE)) {
				$length =  ', length=' . str_replace(array($type, '(', ')', 'varchar', 'int'), array('', '', '', '', ''), $row->Type);
			} else {
				$length = NULL;
			}

			$unique = ($row->Key == 'UNI') ? 'TRUE' : 'FALSE';

			if ($counter != 0) {
				$accessors .= '	';
				$columns .= '	';
			}

			if ($indexCounter != 0) {
				$indexes .= '*   		';
			}

			if ($mutatorCounter != 0) {
				$mutators .= '	';
			}

			/**
			 * Generate the columns and indexes
			 */

			$columns .= '/**' . "\n";

			if ($row->Key == 'PRI') {
				$columns .= '	 * @Id @GeneratedValue' . "\n";
				$columns .= '	 * @Column(type="' . $type . '"' . $length . ', nullable=' . $nullable . ', unique=' . $unique . ')' . "\n";
			} elseif ($row->Key == 'MUL') {
				$indexCounter++;

				$entity = ucfirst(str_replace('_id', '', $row->Field));
				$indexes .= '@index(name="' . $row->Field . '", columns={"' . $row->Field . '"}),' . "\n";
				$type = '\\' . ucfirst($entity);

				$columns .= '	 * @ManyToOne(targetEntity="' . $entity . '")' . "\n";
				$columns .= '	 * @JoinColumns({' . "\n";
				$columns .= '	 *   @JoinColumn(name="' . $row->Field . '", referencedColumnName="' . $row->Field . '", nullable=' . $nullable . ', onDelete="cascade")' . "\n";
				$columns .= '	 * })' . "\n";
			} else {
				$columns .= '	 * @Column(type="' . $type . '"' . $length . ', nullable=' . $nullable . ', unique=' . $unique . ')' . "\n";
			}

			$columns .= '	 */' . "\n";
			$columns .= '	protected $' . $row->Field . ';' . "\n\n";

			/**
			 * Generate the accessors
			 */

			$methodName = 'get_' . $row->Field;
			$methodName = ($input->getOption('snake')) ? Inflect::underscore($methodName) : Inflect::camelize($methodName);

			$accessor = file_get_contents(__DIR__ . '/Templates/Miscellaneous/Accessor.txt');

			$search = array('$field', '$type', '$methodName');
			$replace = array($row->Field, $type, $methodName);

			$accessors .= str_replace($search, $replace, $accessor) . "\n\n";

			/**
			 * Generate the mutators
			 */

			if ($row->Key != 'PRI') {
				$nullable = ($row->Null == 'YES') ? ' = NULL' : NULL;

				$mutatorCounter++;

				if ($row->Key == 'MUL') {
					$class = ucfirst(str_replace('_id', '', $row->Field));
				} elseif ($this->getDataType($row->Type) == 'date' || $this->getDataType($row->Type) == 'datetime' || $this->getDataType($row->Type) == 'datetimetz') {
					$class = 'DateTime';
				} else {
					$class = '$model';
				}

				$methodName = 'set_' . $row->Field;
				$methodName = ($input->getOption('snake')) ? Inflect::underscore($methodName) : Inflect::camelize($methodName);

				$mutator = file_get_contents(__DIR__ . '/Templates/Miscellaneous/Mutator.txt');

				$search = array('$field', '$type', '$methodName', '$class', '$nullable');
				$replace = array($row->Field, $type, $methodName, $class, $nullable);

				$mutators .= str_replace($search, $replace, $mutator) . "\n\n";
			}

			$counter++;
		}

		/**
		 * Search and replace the following keywords from the template
		 */

		$search = array(
			'$indexes',
			'$columns',
			'$accessors',
			'$mutators',
			'$singular',
			'$model'
		);

		$replace = array(
			rtrim($indexes),
			rtrim($columns),
			rtrim($accessors),
			rtrim($mutators),
			Inflect::singularize($input->getArgument('name')),
			ucfirst(Inflect::singularize($input->getArgument('name')))
		);

		$model = str_replace($search, $replace, $model);

		/**
		 * Create a new file and insert the generated template
		 */

		$filename = APPPATH . 'models/' . ucfirst(Inflect::singularize($input->getArgument('name'))) . '.php';

		if (file_exists($filename)) {
			$output->writeln('<error>The ' . Inflect::singularize($input->getArgument('name')) . ' model already exists!</error>');
			
			exit();
		}

		$file = fopen($filename, 'wb');
		file_put_contents($filename, $model);

		$output->writeln('<info>The model "' . Inflect::singularize($input->getArgument('name')) . '" has been created successfully!</info>');
	}
	
}