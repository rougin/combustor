<?php

namespace Rougin\Combustor\Commands;

use Rougin\Combustor\Command;
use Rougin\Combustor\Inflector;

/**
 * @package Combustor
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class CreateRoute extends Command
{
    /**
     * @var string
     */
    protected $name = 'create:controller';

    /**
     * @var string
     */
    protected $description = 'Create a new HTTP controller';

    /**
     * Configures the current command.
     *
     * @return void
     */
    public function init()
    {
        parent::init();

        $this->addOption('doctrine', 'generates a Doctrine-based controller');

        $this->addOption('wildfire', 'generates a Wildfire-based controller');

        $this->addOption('empty', 'generates an empty HTTP controller');
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

        $path = $this->path . '/controllers/';

        $name = Inflector::plural($table);

        $name = ucfirst(Inflector::plural($table));

        $file = $path . $name . '.php';

        if (file_exists($file) && ! $force)
        {
            $this->showFail('"' . $name . '" already exists. Use --force to overwrite the file.');

            return self::RETURN_FAILURE;
        }

        // Create the controller file -------------
        $plate = $this->getTemplate($type);

        if ($empty)
        {
            $plate->setEmpty();
        }

        $plate = $this->maker->make($plate);

        file_put_contents($file, $plate);
        // ----------------------------------------

        $this->showPass('Controller successfully created!');

        return self::RETURN_SUCCESS;
    }
}
