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

        $path = $this->path . '/views/';

        if (! is_dir($path . 'layout'))
        {
            mkdir($path . 'layout');
        }

        // Create the "header.php" file ----------
        $header = new HeaderPlate($bootstrap);

        $file = $path . 'layout/header.php';

        file_put_contents($file, $header->make());
        // ---------------------------------------

        // Create the "footer.php" file ----------
        $footer = new FooterPlate($bootstrap);

        $file = $path . 'layout/footer.php';

        file_put_contents($file, $footer->make());
        // ---------------------------------------

        return self::RETURN_SUCCESS;
    }
}
