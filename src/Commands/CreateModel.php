<?php

namespace Rougin\Combustor\Commands;

use Rougin\Combustor\Command;
use Rougin\Combustor\Inflector;

/**
 * @package Combustor
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class CreateModel extends Command
{
    /**
     * @var string
     */
    protected $name = 'create:model';

    /**
     * @var string
     */
    protected $description = 'Create a new model';

    /**
     * Configures the current command.
     *
     * @return void
     */
    public function init()
    {
        parent::init();

        $this->addOption('doctrine', 'generates a Doctrine-based model');

        $this->addOption('wildfire', 'generates a Wildfire-based model');

        $this->addOption('empty', 'generates an empty model');
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
        $empty = $this->getOption('empty');

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

        $name = Inflector::singular($table);

        $name = ucfirst(Inflector::singular($table));

        $path = $this->path . '/models/';

        $file = $path . $name . '.php';

        if (file_exists($file) && ! $force)
        {
            $this->showFail('"' . $name . '" already exists. Use --force to overwrite the file.');

            return self::RETURN_FAILURE;
        }

        // Create the model file --------------------
        $plate = $this->getTemplate($type);

        if ($empty)
        {
            $plate->setEmpty();
        }

        $plate = $this->maker->make($plate);

        file_put_contents($file, $plate);
        // ------------------------------------------

        $this->showPass('Model successfully created!');

        return self::RETURN_SUCCESS;
    }
}
