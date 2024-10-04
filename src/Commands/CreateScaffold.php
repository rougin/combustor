<?php

namespace Rougin\Combustor\Commands;

use Rougin\Combustor\Combustor;
use Rougin\Combustor\Command;
use Symfony\Component\Console\Command\Command as Symfony;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @package Combustor
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class CreateScaffold extends Symfony
{
    const TYPE_WILDFIRE = 0;

    const TYPE_DOCTRINE = 1;

    /**
     * @var \Rougin\Describe\Driver\DriverInterface|null
     */
    protected $driver = null;

    /**
     * @param \Rougin\Combustor\Combustor $combustor
     */
    public function __construct(Combustor $combustor)
    {
        parent::__construct();

        $this->driver = $combustor->getDriver();
    }

    /**
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->driver !== null;
    }

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName('create:scaffold');

        $this->setDescription('Create a new HTTP controller, model, and view templates');

        $required = InputArgument::REQUIRED;

        $this->addArgument('table', $required, 'Name of the database table');

        $none = InputOption::VALUE_NONE;

        $this->addOption('bootstrap', null, $none, 'adds styling based on Bootstrap');

        $this->addOption('doctrine', null, $none, 'generates Doctrine-based views');

        $this->addOption('wildfire', null, $none, 'generates Wildfire-based views');
    }

    /**
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @param  \Symfony\Component\Console\Output\OutputInterface $output
     * @return integer
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var boolean */
        $doctrine = $input->getOption('doctrine');

        /** @var boolean */
        $wildfire = $input->getOption('wildfire');

        try
        {
            Command::getInstalled($doctrine, $wildfire);
        }
        catch (\Exception $e)
        {
            $text = $e->getMessage();

            $output->writeln('<error>[FAIL] ' . $text . '</error>');

            return self::FAILURE;
        }

        /** @var string */
        $table = $input->getArgument('table');

        $app = $this->getApplication();

        /** @var boolean */
        $bootstrap = $input->getOption('bootstrap');

        // Execute the "create:controller" command ---
        $command = 'create:controller';
        $command = array('command' => $command);
        $command['table'] = $table;
        $command['--doctrine'] = $doctrine;
        $command['--wildfire'] = $wildfire;
        $input = new ArrayInput($command);
        $app->doRun($input, $output);
        // -------------------------------------------

        // Execute the "create:model" command ---
        $command = 'create:model';
        $command = array('command' => $command);
        $command['table'] = $table;
        $command['--doctrine'] = $doctrine;
        $command['--wildfire'] = $wildfire;
        $input = new ArrayInput($command);
        $app->doRun($input, $output);
        // --------------------------------------

        // Execute the "create:views" command ---
        $command = 'create:views';
        $command = array('command' => $command);
        $command['table'] = $table;
        $command['--bootstrap'] = $bootstrap;
        $command['--doctrine'] = $doctrine;
        $command['--wildfire'] = $wildfire;
        $input = new ArrayInput($command);
        $app->doRun($input, $output);
        // --------------------------------------

        return self::SUCCESS;
    }
}