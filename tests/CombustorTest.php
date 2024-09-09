<?php

namespace Rougin\Combustor;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

use Rougin\Combustor\Fixture\CommandBuilder;
use Rougin\Combustor\Fixture\CodeIgniterHelper;

class CombustorTest extends Testcase
{
    /**
     * @var string
     */
    protected $appPath;

    /**
     * @var array
     */
    protected $commands = [
        'Rougin\Combustor\Commands\CreateControllerCommand',
        'Rougin\Combustor\Commands\CreateLayoutCommand',
        'Rougin\Combustor\Commands\CreateModelCommand',
        'Rougin\Combustor\Commands\CreateScaffoldCommand',
        'Rougin\Combustor\Commands\CreateViewCommand',
        'Rougin\Combustor\Commands\InstallDoctrineCommand',
        'Rougin\Combustor\Commands\InstallWildfireCommand',
        'Rougin\Combustor\Commands\RemoveDoctrineCommand',
        'Rougin\Combustor\Commands\RemoveWildfireCommand',
    ];

    /**
     * Sets up the command and the application path.
     *
     * @return void
     */
    public function doSetUp()
    {
        $this->appPath = __DIR__ . '/TestApp/application';
    }

    /**
     * Tests if the initial commands exists.
     *
     * @return void
     */
    public function testCommandsExist()
    {
        CodeIgniterHelper::setDefaults($this->appPath);

        $application = $this->getApplication();

        $this->assertTrue($application->has('create:layout'));
        $this->assertTrue($application->has('install:doctrine'));
        $this->assertTrue($application->has('install:wildfire'));
    }

    /**
     * Gets the application with the loaded classes.
     *
     * @return \Symfony\Component\Console\Application
     */
    protected function getApplication()
    {
        $application = new Application;

        foreach ($this->commands as $commandName) {
            $command = CommandBuilder::create($commandName);

            $application->add($command);
        }

        return $application;
    }
}
