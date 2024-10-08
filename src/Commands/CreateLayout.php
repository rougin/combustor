<?php

namespace Rougin\Combustor\Commands;

use Rougin\Combustor\Command;
use Rougin\Combustor\Template\FooterPlate;
use Rougin\Combustor\Template\HeaderPlate;

/**
 * @package Combustor
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class CreateLayout extends Command
{
    /**
     * @var string
     */
    protected $name = 'create:layout';

    /**
     * @var string
     */
    protected $description = 'Create a new header and footer file';

    /**
     * Configures the current command.
     *
     * @return void
     */
    public function init()
    {
        $this->addOption('bootstrap', 'adds styling based on Bootstrap');

        $this->addOption('force', 'generates file/s even they already exists');
    }

    /**
     * Executes the command.
     *
     * @return integer
     */
    public function run()
    {
        /** @var boolean */
        $bootstrap = $this->getOption('bootstrap');

        /** @var boolean */
        $force = $this->getOption('force');

        $path = $this->path . '/views/';

        $file = $path . 'layout/header.php';

        if (is_dir($path . 'layout') && ! $force)
        {
            $this->showFail('"header.php", "footer.php" already exists. Use --force to overwrite them.');

            return self::RETURN_FAILURE;
        }

        if (! is_dir($path . 'layout'))
        {
            mkdir($path . 'layout');
        }

        // Create the "header.php" file ----------
        $header = new HeaderPlate($bootstrap);

        file_put_contents($file, $header->make());
        // ---------------------------------------

        // Create the "footer.php" file ----------
        $footer = new FooterPlate($bootstrap);

        $file = $path . 'layout/footer.php';

        file_put_contents($file, $footer->make());
        // ---------------------------------------

        $this->showPass('Layout files created!');

        return self::RETURN_SUCCESS;
    }
}
