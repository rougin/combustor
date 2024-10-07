<?php

namespace Rougin\Combustor\Commands;

use Rougin\Combustor\Command;
use Rougin\Combustor\Inflector;

/**
 * @package Combustor
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class CreateRepo extends Command
{
    /**
     * @var string
     */
    protected $name = 'create:repository';

    /**
     * @var string
     */
    protected $description = 'Create a new entity repository';

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
        $force = $this->getOption('force');

        $name = Inflector::singular($table);

        $name = ucfirst(Inflector::singular($table));

        $name = $name . '_repository';

        $path = $this->path . '/repositories/';

        $file = $path . $name . '.php';

        if (file_exists($file) && ! $force)
        {
            $this->showFail('"' . $name . '" already exists. Use --force to overwrite the file.');

            return self::RETURN_FAILURE;
        }

        // Create the repository file -------------------
        if (! is_dir($path))
        {
            mkdir($path);
        }

        $plate = $this->getTemplate(self::TYPE_DOCTRINE);

        $plate = $this->maker->make($plate);

        file_put_contents($file, $plate);
        // ----------------------------------------------

        $this->showPass('Repository successfully created!');

        return self::RETURN_SUCCESS;
    }
}
