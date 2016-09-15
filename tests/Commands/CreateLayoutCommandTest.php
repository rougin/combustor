<?php

namespace Rougin\Combustor\Commands;

use Symfony\Component\Console\Tester\CommandTester;

use Rougin\Combustor\Fixture\CommandBuilder;
use Rougin\Combustor\Fixture\CodeIgniterHelper;

use PHPUnit_Framework_TestCase;

class CreateLayoutCommandTest extends PHPUnit_Framework_TestCase
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
        $command = 'Rougin\Combustor\Commands\CreateLayoutCommand';

        $this->command = CommandBuilder::create($command);
    }

    /**
     * Tests if the expected file exists after executing the command.
     *
     * @return void
     */
    public function testFilesExist()
    {
        CodeIgniterHelper::setDefaults($this->appPath);

        $command = new CommandTester($this->command);
        $command->execute([ '--bootstrap' => true]);

        $header = $this->appPath . '/views/layout/header.php';
        $footer = $this->appPath . '/views/layout/footer.php';

        $this->assertFileExists($header);
        $this->assertFileExists($footer);

        CodeIgniterHelper::setDefaults($this->appPath);
    }

    /**
     * Tests if the folder already exists.
     *
     * @return void
     */
    public function testFolderExists()
    {
        $command = new CommandTester($this->command);

        $command->execute([ '--bootstrap' => true]);
        $command->execute([ '--bootstrap' => true]);

        $message = 'The layout directory already exists!' . PHP_EOL;

        $this->assertEquals($message, $command->getDisplay());

        CodeIgniterHelper::setDefaults($this->appPath);
    }
}
