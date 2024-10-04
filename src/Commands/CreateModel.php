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

        try
        {
            $type = $this->getInstalled($doctrine, $wildfire);
        }
        catch (\Exception $e)
        {
            $this->showFail($e->getMessage());

            return self::RETURN_FAILURE;
        }

        // Create the model file --------------------
        $name = Inflector::singular($table);

        $name = ucfirst(Inflector::singular($table));

        $path = $this->path . '/models/';

        $file = $path . $name . '.php';

        $plate = $this->getTemplate($type);

        $plate = $this->maker->make($plate);

        file_put_contents($file, $plate);
        // ------------------------------------------

        $this->showPass('Model successfully created!');

        return self::RETURN_SUCCESS;
    }
}
