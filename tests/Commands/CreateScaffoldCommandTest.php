<?php

namespace Rougin\Combustor\Commands;

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
     * @var string
     */
    protected $table = 'post';

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
            __DIR__ . '/../../src/Templates/Libraries/Wildfire.tpl' =>
            $this->appPath . '/libraries/Wildfire.php',
            __DIR__ . '/../../src/Templates/Views/Layout/header.tpl' =>
            $this->appPath . '/views/layout/header.php',
            __DIR__ . '/../../src/Templates/Views/Layout/footer.tpl' =>
            $this->appPath . '/views/layout/footer.php'
        ];

        foreach ($files as $source => $destination) {
            copy($source, $destination);
        }

        $application = $this->getApplication();

        $command = $application->find('create:scaffold');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'name' => $this->table,
            '--bootstrap' => true
        ]);

        $controller = $this->appPath . '/controllers/' . ucfirst(plural($this->table)) . '.php';
        $model = $this->appPath . '/models/' . ucfirst(singular($this->table)) . '.php';

        $this->assertFileExists($controller);
        $this->assertFileExists($model);

        $create = $this->appPath . '/views/' . plural($this->table) . '/create.php';
        $edit = $this->appPath . '/views/' . plural($this->table) . '/edit.php';
        $index = $this->appPath . '/views/' . plural($this->table) . '/index.php';
        $show = $this->appPath . '/views/' . plural($this->table) . '/show.php';

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
