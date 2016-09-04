<?php

namespace Rougin\Combustor\Commands;

use Twig_Environment;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use Rougin\Describe\Describe;
use Rougin\Combustor\Common\Tools;

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
     * @var string
     */
    protected $command = '';

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

    /**
     * Set the configurations of the specified command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('create:' . $this->command)
            ->setDescription('Create a new ' . $this->command)
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'Name of the table'
            )->addOption(
                'bootstrap',
                null,
                InputOption::VALUE_NONE,
                'Includes the Bootstrap CSS/JS Framework tags'
            )->addOption(
                'camel',
                null,
                InputOption::VALUE_NONE,
                'Uses the camel case naming convention'
            )->addOption(
                'keep',
                null,
                InputOption::VALUE_NONE,
                'Keeps the name to be used'
            );

        if ($this->command == 'controller') {
            $this->addOption(
                'lowercase',
                null,
                InputOption::VALUE_NONE,
                'Keeps the first character of the name to lowercase'
            );
        }
    }

    /**
     * Checks whether the command is enabled or not in the current environment.
     *
     * @return bool
     */
    public function isEnabled()
    {
        return Tools::isCommandEnabled();
    }
}
