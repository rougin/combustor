<?php

namespace Rougin\Combustor\Commands;

use Rougin\Combustor\Command;
use Rougin\Combustor\Inflector;
use Rougin\Combustor\Template\CreatePlate;
use Rougin\Combustor\Template\EditPlate;
use Rougin\Combustor\Template\IndexPlate;

/**
 * @package Combustor
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class CreateView extends Command
{
    /**
     * @var string
     */
    protected $name = 'create:views';

    /**
     * @var string
     */
    protected $description = 'Create view templates';

    /**
     * Configures the current command.
     *
     * @return void
     */
    public function init()
    {
        parent::init();

        $this->addOption('bootstrap', 'adds styling based on Bootstrap');

        $this->addOption('doctrine', 'generates Doctrine-based views');

        $this->addOption('wildfire', 'generates Wildfire-based views');
    }

    /**
     * Executes the command.
     *
     * @return integer
     */
    public function run()
    {
        /** @var string */
        $table = $this->getArgument('table');

        /** @var boolean */
        $doctrine = $this->getOption('doctrine');

        /** @var boolean */
        $wildfire = $this->getOption('wildfire');

        /** @var boolean */
        $force = $this->getOption('force');

        try
        {
            $type = $this->getInstalled($doctrine, $wildfire);
        }
        catch (\Exception $e)
        {
            $this->showFail($e->getMessage());

            return self::RETURN_FAILURE;
        }

        /** @var \Rougin\Describe\Driver\DriverInterface */
        $describe = $this->driver;

        $cols = $describe->columns($table);

        $name = Inflector::plural($table);

        $path = $this->path . '/views/';

        if (is_dir($path . $name) && ! $force)
        {
            $this->showFail('"' . $name . '" directory already exists. Use --force to overwrite the directory.');

            return self::RETURN_FAILURE;
        }

        if (! is_dir($path . $name))
        {
            mkdir($path . $name);
        }

        /** @var boolean */
        $bootstrap = $this->getOption('bootstrap');

        // Create the "create.php" file ----------------
        $create = new CreatePlate($table, $type, $cols);

        $create->withBootstrap($bootstrap);

        $create->withExcludedFields($this->excluded);

        $create->withCustomFields($this->customs);

        $file = $path . $name . '/create.php';

        file_put_contents($file, $create->make('  '));
        // ---------------------------------------------

        // Create the "edit.php" file --------------
        $edit = new EditPlate($table, $type, $cols);

        $edit->withBootstrap($bootstrap);

        $edit->withExcludedFields($this->excluded);

        $edit->withCustomFields($this->customs);

        $file = $path . $name . '/edit.php';

        file_put_contents($file, $edit->make('  '));
        // -----------------------------------------

        // Create the "index.php" file ---------------
        $index = new IndexPlate($table, $type, $cols);

        $index->withBootstrap($bootstrap);

        $file = $path . $name . '/index.php';

        file_put_contents($file, $index->make('  '));
        // -------------------------------------------

        $this->showPass('Views successfully created!');

        return self::RETURN_SUCCESS;
    }
}
