<?php

namespace Rougin\Combustor\Commands;

use Rougin\Combustor\Command;

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

        $this->addOption('doctrine', 'generates the controller based from Doctrine');

        $this->addOption('wildfire', 'generates the controller based from Wildfire');
    }
}
