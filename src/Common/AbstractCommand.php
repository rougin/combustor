<?php

namespace Rougin\Combustor\Common;

use Rougin\Describe\Describe;
use Symfony\Component\Console\Command\Command;
use Twig_Environment;

/**
 * Abstract Command
 *
 * Extends the Symfony\Console\Command class with Twig's renderer.
 * 
 * @package Combustor
 * @author  Rougin Royce Gutib <rougingutib@gmail.com>
 */
abstract class AbstractCommand extends Command
{
    protected $renderer;

    /**
     * @param Twig_Environment $renderer
     * @param Describe         $describe
     */
    public function __construct(Describe $describe, Twig_Environment $renderer)
    {
        parent::__construct();

        $this->describe = $describe;
        $this->renderer = $renderer;
    }
}
