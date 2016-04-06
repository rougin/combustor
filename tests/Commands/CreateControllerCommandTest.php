<?php

namespace Rougin\Combustor;

use Symfony\Component\Console\Tester\CommandTester;

use Rougin\Combustor\Fixture\CommandBuilder;
use Rougin\Combustor\Fixture\CodeIgniterHelper;

use PHPUnit_Framework_TestCase;

class CreateControllerCommandTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Symfony\Component\Console\Command\Command
     */
    protected $createCommand;

    /**
     * @var string
     */
    protected $appPath;

    /**
     * Sets up the command and the application path.
     *
     * @return void
     */
    public function setUp()
    {
        $this->appPath = __DIR__ . '/../TestApp/application';

        $createCommand = 'Rougin\Combustor\Commands\CreateControllerCommand';

        $this->createCommand = CommandBuilder::create($createCommand);
    }

    /**
     * Tests if the expected Wildfire controller is created.
     * 
     * @return void
     */
    public function testWildfireControllerIsCreated()
    {
        CodeIgniterHelper::setDefaults($this->appPath);

        $wildfireCommand = 'Rougin\Combustor\Commands\InstallWildfireCommand';
        $wildfire = CommandBuilder::create($wildfireCommand);

        $installCommand = new CommandTester($wildfire);
        $installCommand->execute([]);

        $createCommand = new CommandTester($this->createCommand);
        $createCommand->execute([
            'name' => 'users',
            '--camel' => false,
            '--keep' => true
        ]);

        $file = $this->appPath . '/controllers/Users.php';

        $this->assertFileExists($file);

        CodeIgniterHelper::setDefaults($this->appPath);
    }

    /**
     * Tests if the expected Doctrine controller is created.
     * 
     * @return void
     */
    public function testDoctrineControllerIsCreated()
    {
        CodeIgniterHelper::setDefaults($this->appPath);

        $doctrineCommand = 'Rougin\Combustor\Commands\InstallDoctrineCommand';
        $doctrine = CommandBuilder::create($doctrineCommand);

        $installCommand = new CommandTester($doctrine);
        $installCommand->execute([]);

        $createCommand = new CommandTester($this->createCommand);
        $createCommand->execute([
            'name' => 'users',
            '--camel' => false,
            '--keep' => true
        ]);

        $file = $this->appPath . '/controllers/Users.php';

        $this->assertFileExists($file);

        CodeIgniterHelper::setDefaults($this->appPath);
    }
}
