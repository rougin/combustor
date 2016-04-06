<?php

namespace Rougin\Combustor;

use Symfony\Component\Console\Tester\CommandTester;

use Rougin\Combustor\Fixture\CommandBuilder;
use Rougin\Combustor\Fixture\CodeIgniterHelper;

use PHPUnit_Framework_TestCase;

class CreateViewCommandTest extends PHPUnit_Framework_TestCase
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

        $createCommand = 'Rougin\Combustor\Commands\CreateViewCommand';

        $this->createCommand = CommandBuilder::create($createCommand);
    }

    /**
     * Tests if the expected views are created.
     * 
     * @return void
     */
    public function testViewsAreCreated()
    {
        CodeIgniterHelper::setDefaults($this->appPath);

        $createCommand = new CommandTester($this->createCommand);
        $createCommand->execute([
            'name' => 'users',
            '--camel' => true,
            '--bootstrap' => true,
            '--keep' => false
        ]);

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
}
