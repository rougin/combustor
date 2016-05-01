<?php

namespace Rougin\Combustor\Commands;

use Symfony\Component\Console\Tester\CommandTester;

use Rougin\Combustor\Common\Config;
use Rougin\Combustor\Fixture\CommandBuilder;
use Rougin\Combustor\Fixture\CodeIgniterHelper;

use PHPUnit_Framework_TestCase;

class InstallDoctrineCommandTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $appPath;

    /**
     * @var \Symfony\Component\Console\Command\Command
     */
    protected $command;

    /**
     * Sets up the command and the application path.
     *
     * @return void
     */
    public function setUp()
    {
        $this->appPath = __DIR__ . '/../TestApp/application';
        $command = 'Rougin\Combustor\Commands\InstallDoctrineCommand';

        $this->command = CommandBuilder::create($command);
    }

    /**
     * Tests if the expected file exists after executing the command.
     * 
     * @return void
     */
    public function testFileExists()
    {
        CodeIgniterHelper::setDefaults($this->appPath);

        $command = new CommandTester($this->command);
        $command->execute([]);

        $file = $this->appPath . '/libraries/Doctrine.php';

        $this->assertFileExists($file);

        $autoload = new Config('autoload', $this->appPath . '/config');

        $drivers = [ 'session' ];
        $helpers = [ 'form', 'url' ];

        $this->assertEquals($drivers, $autoload->get('drivers', 75, 'array'));
        $this->assertEquals($helpers, $autoload->get('helper', 85, 'array'));

        $config = new Config('config', $this->appPath . '/config');

        $composerAutoload = 'realpath(\'vendor\') . \'/autoload.php\'';

        $this->assertEquals($composerAutoload, $config->get('composer_autoload', 132, 'string'));
        $this->assertEmpty($config->get('index_page', 31, 'string'));
        $this->assertEquals(md5('rougin'), $config->get('encryption_key', 310, 'string'));

        CodeIgniterHelper::setDefaults($this->appPath);
    }
}
