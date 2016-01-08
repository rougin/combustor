<?php

namespace Rougin\Combustor\Common;

use Twig_Environment;
use Rougin\Describe\Describe;
use Symfony\Component\Console\Command\Command;

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
    /**
     * @var \Rougin\Describe\Describe
     */
    protected $describe;

    /**
     * @var \Twig_Environment
     */
    protected $renderer;

    /**
     * @param \Twig_Environment         $renderer
     * @param \Rougin\Describe\Describe $describe
     */
    public function __construct(Describe $describe, Twig_Environment $renderer)
    {
        parent::__construct();

        $this->describe = $describe;
        $this->renderer = $renderer;
    }
}
