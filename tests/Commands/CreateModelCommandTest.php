<?php

namespace Rougin\Combustor;

use Symfony\Component\Console\Tester\CommandTester;

use Rougin\Combustor\Fixture\CommandBuilder;
use Rougin\Combustor\Fixture\CodeIgniterHelper;

class CreateModelCommandTest extends Testcase
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
     * @var string
     */
    protected $table = 'post';

    /**
     * Sets up the command and the application path.
     *
     * @return void
     */
    public function doSetUp()
    {
        $this->appPath = __DIR__ . '/../TestApp/application';

        $createCommand = 'Rougin\Combustor\Commands\CreateModelCommand';

        $this->createCommand = CommandBuilder::create($createCommand);
    }

    /**
     * Tests if the expected Wildfire model is created.
     *
     * @return void
     */
    public function testWildfireModelIsCreated()
    {
        CodeIgniterHelper::setDefaults($this->appPath);

        $wildfireCommand = 'Rougin\Combustor\Commands\InstallWildfireCommand';
        $wildfire = CommandBuilder::create($wildfireCommand);

        $installCommand = new CommandTester($wildfire);
        $installCommand->execute([]);

        $createCommand = new CommandTester($this->createCommand);
        $createCommand->execute([
            'name' => $this->table,
            '--camel' => false
        ]);

        $file = $this->appPath . '/models/' . ucfirst(singular($this->table)) . '.php';

        $this->assertFileExists($file);

        CodeIgniterHelper::setDefaults($this->appPath);
    }

    /**
     * Tests if the expected Doctrine model is created.
     *
     * @return void
     */
    public function testDoctrineModelIsCreated()
    {
        CodeIgniterHelper::setDefaults($this->appPath);

        $doctrineCommand = 'Rougin\Combustor\Commands\InstallDoctrineCommand';
        $doctrine = CommandBuilder::create($doctrineCommand);

        $installCommand = new CommandTester($doctrine);
        $installCommand->execute([]);

        $createCommand = new CommandTester($this->createCommand);
        $createCommand->execute([
            'name' => $this->table,
            '--camel' => false
        ]);

        $file = $this->appPath . '/models/' . ucfirst(singular($this->table)) . '.php';

        $this->assertFileExists($file);

        CodeIgniterHelper::setDefaults($this->appPath);
    }

    /**
     * Tests if command prompts an error if there is no library installed.
     *
     * @return void
     */
    public function testNoLibraryInstalled()
    {
        CodeIgniterHelper::setDefaults($this->appPath);

        $createCommand = new CommandTester($this->createCommand);
        $createCommand->execute([
            'name' => $this->table,
            '--camel' => false
        ]);

        $expected = 'Please install Wildfire or Doctrine!' . PHP_EOL;

        $this->assertRegExp('/Please install Wildfire or Doctrine/', $createCommand->getDisplay());

        CodeIgniterHelper::setDefaults($this->appPath);
    }
}
