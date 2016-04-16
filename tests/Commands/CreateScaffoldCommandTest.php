<?php

namespace Rougin\Combustor;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

use Rougin\Combustor\Fixture\CommandBuilder;
use Rougin\Combustor\Fixture\CodeIgniterHelper;

use PHPUnit_Framework_TestCase;

class CreateScaffoldCommandTest extends PHPUnit_Framework_TestCase
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
        'Rougin\Combustor\Commands\CreateModelCommand',
        'Rougin\Combustor\Commands\CreateScaffoldCommand',
        'Rougin\Combustor\Commands\CreateViewCommand',
    ];

    /**
     * Sets up the command and the application path.
     *
     * @return void
     */
    public function setUp()
    {
        $this->appPath = __DIR__ . '/../TestApp/application';
    }

    /**
     * Tests if the initial commands exists.
     * 
     * @return void
     */
    public function testCommandsExist()
    {
        CodeIgniterHelper::setDefaults($this->appPath);

        mkdir($this->appPath . '/views/layout');

        $files = [
            __DIR__ . '/../../src/Templates/Libraries/Wildfire.template' =>
            $this->appPath . '/libraries/Wildfire.php',
            __DIR__ . '/../../src/Templates/Views/Layout/header.template' =>
            $this->appPath . '/views/layout/header.php',
            __DIR__ . '/../../src/Templates/Views/Layout/footer.template' =>
            $this->appPath . '/views/layout/footer.php'
        ];

        foreach ($files as $source => $destination) {
            copy($source, $destination);
        }

        $application = $this->getApplication();

        $command = $application->find('create:scaffold');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'name' => 'users',
            '--bootstrap' => true
        ]);

        $this->assertFileExists($this->appPath . '/controllers/Users.php');
        $this->assertFileExists($this->appPath . '/models/Users.php');

        $create = $file = $this->appPath . '/views/users/create.php';
        $edit = $file = $this->appPath . '/views/users/edit.php';
        $index = $file = $this->appPath . '/views/users/index.php';
        $show = $file = $this->appPath . '/views/users/show.php';

        $this->assertFileExists($create);
        $this->assertFileExists($edit);
        $this->assertFileExists($index);
        $this->assertFileExists($show);

        CodeIgniterHelper::setDefaults($this->appPath);
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
