<?php

namespace Rougin\Combustor\Commands;

use Symfony\Component\Console\Tester\CommandTester;

use Rougin\Combustor\Fixture\CommandBuilder;
use Rougin\Combustor\Fixture\CodeIgniterHelper;

use PHPUnit_Framework_TestCase;

class InstallDoctrineCommandTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Symfony\Component\Console\Command\Command
     */
    protected $command;

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
        $command = 'Rougin\Combustor\Commands\InstallDoctrineCommand';

        CodeIgniterHelper::setDefaults($this->appPath);
        $this->command = CommandBuilder::create($command);
    }

    /**
     * Tests if the expected file exists after executing the command.
     * 
     * @return void
     */
    public function testFileExists()
    {
        $command = new CommandTester($this->command);
        $command->execute([]);

        $file = $this->appPath . '/libraries/Doctrine.php';

        $this->assertFileExists($file);
        CodeIgniterHelper::setDefaults($this->appPath);
    }
}
