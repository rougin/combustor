<?php

namespace Rougin\Combustor\Commands;

use Rougin\Combustor\Command;

/**
 * @package Combustor
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class CreateScaffold extends Command
{
    const TYPE_WILDFIRE = 0;

    const TYPE_DOCTRINE = 1;

    /**
     * @var \Rougin\Describe\Driver\DriverInterface|null
     */
    protected $driver = null;

    /**
     * @var string
     */
    protected $name = 'create:scaffold';

    /**
     * @var string
     */
    protected $description = 'Create a new HTTP controller, model, and view templates';

    /**
     * Configures the current command.
     *
     * @return void
     */
    public function init()
    {
        parent::init();

        $this->addOption('bootstrap', 'adds styling based on Bootstrap');

        $this->addOption('doctrine', 'generates a Doctrine-based controller, models, and views');

        $this->addOption('wildfire', 'generates a Wildfire-based controller, models, and views');

        $this->addOption('empty', 'generates an empty HTTP controller and model');
    }

    /**
     * Executes the command.
     *
     * @return integer
     */
    public function run()
    {
        /** @var boolean */
        $doctrine = $this->getOption('doctrine');

        /** @var boolean */
        $empty = $this->getOption('empty');

        /** @var boolean */
        $wildfire = $this->getOption('wildfire');

        /** @var boolean */
        $force = $this->getOption('force');

        try
        {
            $this->getInstalled($doctrine, $wildfire);
        }
        catch (\Exception $e)
        {
            $this->showFail($e->getMessage());

            return Command::RETURN_FAILURE;
        }

        /** @var string */
        $table = $this->getArgument('table');

        $input = array('table' => $table);
        $input['--doctrine'] = $doctrine;
        $input['--empty'] = $empty;
        $input['--wildfire'] = $wildfire;
        $input['--force'] = $force;

        // Execute the "create:controller" command ----
        $this->runCommand('create:controller', $input);
        // --------------------------------------------

        // Execute the "create:model" command ----
        $this->runCommand('create:model', $input);
        // ---------------------------------------

        // Execute the "create:views" command -----
        /** @var boolean */
        $bootstrap = $this->getOption('bootstrap');

        unset($input['--empty']);

        $input['--bootstrap'] = $bootstrap;

        $this->runCommand('create:views', $input);
        // ----------------------------------------

        // Execute the "create:repository" command ----
        $input = array('table' => $table);

        $this->runCommand('create:repository', $input);
        // --------------------------------------------

        return Command::RETURN_SUCCESS;
    }
}
