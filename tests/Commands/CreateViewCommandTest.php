<?php

namespace Rougin\Combustor\Commands;

use Symfony\Component\Console\Tester\CommandTester;

use Rougin\Combustor\Fixture\CommandBuilder;
use Rougin\Combustor\Fixture\CodeIgniterHelper;

use Rougin\Combustor\Testcase;

class CreateViewCommandTest extends Testcase
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
            'name' => $this->table,
            '--camel' => true,
            '--bootstrap' => true,
            '--keep' => true
        ]);

        $create = $this->appPath . '/views/' . $this->table . '/create.php';
        $edit = $this->appPath . '/views/' . $this->table . '/edit.php';
        $index = $this->appPath . '/views/' . $this->table . '/index.php';
        $show = $this->appPath . '/views/' . $this->table . '/show.php';

        $this->assertFileExists($create);
        $this->assertFileExists($edit);
        $this->assertFileExists($index);
        $this->assertFileExists($show);

        CodeIgniterHelper::setDefaults($this->appPath);
    }

    /**
     * Tests if the command prompts an error if the folder already exists.
     *
     * @return void
     */
    public function testFolderAlreadyExists()
    {
        CodeIgniterHelper::setDefaults($this->appPath);

        $options = [
            'name' => $this->table,
            '--camel' => true,
            '--bootstrap' => true,
            '--keep' => false
        ];

        $createCommand = new CommandTester($this->createCommand);
        $createCommand->execute($options);

        $createCommand = new CommandTester($this->createCommand);
        $createCommand->execute($options);

        $this->assertRegExp('/views folder already exists/', $createCommand->getDisplay());

        CodeIgniterHelper::setDefaults($this->appPath);
    }
}
