<?php

namespace Rougin\Combustor\Commands;

use Rougin\Describe\Describe;
use League\Flysystem\Filesystem;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Abstract Command
 *
 * @package Combustor
 * @author  Rougin Royce Gutib <rougingutib@gmail.com>
 */
abstract class AbstractCommand extends \Symfony\Component\Console\Command\Command
{
    /**
     * @var \CI_Controller
     */
    protected $codeigniter;

    /**
     * @var string
     */
    protected $command;

    /**
     * @var \Rougin\Describe\Describe
     */
    protected $describe;

    /**
     * @var \League\Flysystem\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Twig_Environment
     */
    protected $renderer;

    /**
     * @param \CI_Controller               $codeigniter
     * @param \Rougin\Describe\Describe    $describe
     * @param \League\Flysystem\Filesystem $filesystem
     * @param \Twig_Environment            $renderer
     */
    public function __construct(\CI_Controller $codeigniter, Describe $describe, Filesystem $filesystem, \Twig_Environment $renderer = null)
    {
        parent::__construct();

        $this->codeigniter = $codeigniter;
        $this->describe    = $describe;
        $this->filesystem  = $filesystem;
        $this->renderer    = $renderer;
    }
}
