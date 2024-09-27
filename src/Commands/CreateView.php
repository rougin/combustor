<?php

namespace Rougin\Combustor\Commands;

use Rougin\Combustor\Command;
use Rougin\Combustor\Inflector;
use Rougin\Combustor\Template\IndexPlate;

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
    protected $description = 'Creates a new model';

    /**
     * Configures the current command.
     *
     * @return void
     */
    public function init()
    {
        parent::init();

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
        $table = $this->getArgument('name');

        try
        {
            $type = $this->getInstalled();
        }
        catch (\Exception $e)
        {
            $this->showFail($e->getMessage());

            return self::RETURN_FAILURE;
        }

        $name = Inflector::plural($table);

        $path = $this->path . '/views/';

        // Create the "index.php" file ----------
        $index = new IndexPlate($table, $type);

        $file = $path . $name . '/index.php';

        file_put_contents($file, $index->make());
        // --------------------------------------

        $this->showPass('Views successfully created!');

        return self::RETURN_SUCCESS;
    }
}
