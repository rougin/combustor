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

        // Create the repository file -------------------
        $name = Inflector::singular($table);

        $name = ucfirst(Inflector::singular($table));

        $path = $this->path . '/repositories/';

        if (! is_dir($path))
        {
            mkdir($path);
        }

        $file = $path . $name . '_repository.php';

        $plate = $this->getTemplate(self::TYPE_DOCTRINE);

        $plate = $this->maker->make($plate);

        file_put_contents($file, $plate);
        // ----------------------------------------------

        $this->showPass('Repository successfully created!');

        return self::RETURN_SUCCESS;
    }
}
