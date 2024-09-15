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
}
