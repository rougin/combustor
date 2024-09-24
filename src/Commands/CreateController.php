<?php

namespace Rougin\Combustor\Commands;

use Rougin\Combustor\Command;

/**
 * @package Combustor
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class CreateController extends Command
{
    /**
     * @var string
     */
    protected $name = 'create:controller';

    /**
     * @var string
     */
    protected $description = 'Creates a new HTTP controller';

    /**
     * Configures the current command.
     *
     * @return void
     */
    public function init()
    {
        parent::init();

        $this->addOption('doctrine', 'generates the controller based from Doctrine');

        $this->addOption('wildfire', 'generates the controller based from Wildfire');
    }
}
