<?php

namespace Rougin\Combustor\Commands;

use Symfony\Component\Console\Tester\CommandTester;

use Rougin\Combustor\Fixture\CommandBuilder;
use Rougin\Combustor\Fixture\CodeIgniterHelper;

use Rougin\Combustor\Testcase;

class RemoveDoctrineCommandTest extends Testcase
{
    /**
     * @var \Symfony\Component\Console\Command\Command
     */
    protected $installCommand;

    /**
     * @var \Symfony\Component\Console\Command\Command
     */
    protected $removeCommand;

    /**
     * @var string
     */
    protected $appPath;

    /**
     * Sets up the command and the application path.
     *
     * @return void
     */
    public function doSetUp()
    {
        $this->appPath = __DIR__ . '/../TestApp/application';

        $installCommand = 'Rougin\Combustor\Commands\InstallDoctrineCommand';
        $removeCommand = 'Rougin\Combustor\Commands\RemoveDoctrineCommand';

        CodeIgniterHelper::setDefaults($this->appPath);

        $this->installCommand = CommandBuilder::create($installCommand);
        $this->removeCommand = CommandBuilder::create($removeCommand);
    }

    /**
     * Tests if the expected file exists after executing the command.
     *
     * @return void
     */
    public function testFileExists()
    {
        $installCommand = new CommandTester($this->installCommand);
        $installCommand->execute([]);

        $removeCommand = new CommandTester($this->removeCommand);
        $removeCommand->execute([]);

        $file = $this->appPath . '/libraries/Doctrine.php';

        $this->assertTrue(! file_exists($file));

        CodeIgniterHelper::setDefaults($this->appPath);
    }
}
