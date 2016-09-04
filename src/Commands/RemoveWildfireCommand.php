<?php

namespace Rougin\Combustor\Commands;

use Rougin\Combustor\Common\Commands\RemoveCommand;

/**
 * Remove Wildfire Command
 *
 * Removes Wildfire from CodeIgniter
 *
 * @package Combustor
 * @author  Rougin Royce Gutib <rougingutib@gmail.com>
 */
class RemoveWildfireCommand extends RemoveCommand
{
    /**
     * @var string
     */
    protected $library = 'wildfire';
}
