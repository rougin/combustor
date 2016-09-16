<?php

namespace Rougin\Combustor;

use Symfony\Component\Console\Tester\CommandTester;

use Rougin\Combustor\Fixture\CommandBuilder;
use Rougin\Combustor\Fixture\CodeIgniterHelper;

use PHPUnit_Framework_TestCase;

class ValidatorTest extends PHPUnit_Framework_TestCase
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
    public function setUp()
    {
        $this->appPath = __DIR__ . '/TestApp/application';

        $createCommand = 'Rougin\Combustor\Commands\CreateControllerCommand';

        $this->createCommand = CommandBuilder::create($createCommand);
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
            '--camel' => false,
            '--keep' => true
        ]);

        $this->assertRegExp('/Please install Wildfire or Doctrine/', $createCommand->getDisplay());

        CodeIgniterHelper::setDefaults($this->appPath);
    }

    /**
     * Tests if command prompts an error if user does not select a library.
     *
     * @return void
     */
    public function testNoLibrarySelected()
    {
        CodeIgniterHelper::setDefaults($this->appPath);

        $templatesPath = __DIR__ . '/../src/Templates';

        $files = [
            $templatesPath . '/Libraries/Wildfire.tpl' =>
            $this->appPath . '/libraries/Wildfire.php',
            $templatesPath . '/Libraries/Doctrine.tpl' =>
            $this->appPath . '/libraries/Doctrine.php',
        ];

        foreach ($files as $source => $destination) {
            copy($source, $destination);
        }

        $createCommand = new CommandTester($this->createCommand);
        $createCommand->execute([
            'name' => $this->table,
            '--camel' => false,
            '--keep' => false
        ]);

        $this->assertRegExp('/Choose only one/', $createCommand->getDisplay());

        CodeIgniterHelper::setDefaults($this->appPath);
    }

    /**
     * Tests if the generated file already exists.
     *
     * @return void
     */
    public function testFileExists()
    {
        CodeIgniterHelper::setDefaults($this->appPath);

        $wildfireCommand = 'Rougin\Combustor\Commands\InstallWildfireCommand';
        $wildfire = CommandBuilder::create($wildfireCommand);

        $installCommand = new CommandTester($wildfire);
        $installCommand->execute([]);

        $createCommand = new CommandTester($this->createCommand);
        $createCommand->execute([
            'name' => $this->table,
            '--camel' => false,
            '--keep' => true
        ]);

        $createCommand = new CommandTester($this->createCommand);
        $createCommand->execute([
            'name' => $this->table,
            '--camel' => false,
            '--keep' => true
        ]);

        $this->assertRegExp('/controller already exists/', $createCommand->getDisplay());

        CodeIgniterHelper::setDefaults($this->appPath);
    }

    /**
     * Tests if the command prompts an error if camel is used in Wildfire.
     *
     * @return void
     */
    public function testWildfireCamelCasing()
    {
        CodeIgniterHelper::setDefaults($this->appPath);

        $wildfireCommand = 'Rougin\Combustor\Commands\InstallWildfireCommand';
        $wildfire = CommandBuilder::create($wildfireCommand);

        $installCommand = new CommandTester($wildfire);
        $installCommand->execute([]);

        $createCommand = new CommandTester($this->createCommand);
        $createCommand->execute([
            'name' => $this->table,
            '--camel' => true,
            '--keep' => true
        ]);

        $this->assertRegExp('/not support camel casing/', $createCommand->getDisplay());

        CodeIgniterHelper::setDefaults($this->appPath);
    }
}
