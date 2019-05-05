<?php

namespace Rougin\Combustor\Commands;

use Rougin\Combustor\Common\Commands\RemoveCommand;

/**
 * Remove Doctrine Command
 *
 * Removes Doctrine from CodeIgniter
 *
 * @package Combustor
 * @author  Rougin Gutib <rougingutib@gmail.com>
 */
class RemoveDoctrineCommand extends RemoveCommand
{
    /**
     * @var string
     */
    protected $library = 'doctrine';
}
