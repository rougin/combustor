<?php

namespace Rougin\Combustor\Commands;

use Symfony\Component\Console\Tester\CommandTester;

class MakeControllerCommandTest extends \Rougin\Combustor\TestCase
{
    /**
     * @var string
     */
    protected $command = 'Rougin\Combustor\Commands\CreateControllerCommand';

    /**
     * @var string
     */
    protected $table = 'post';

    /**
     * Tests if the command works.
     *
     * @return void
     */
    public function testCommand()
    {
        $this->setDefaults();

        $tester = new CommandTester($this->command);

        $tester->execute([ 'name' => $this->table ]);

        $file = $this->path . '/controllers/' . ucfirst(plural($this->table)) . '.php';

        $this->assertFileExists($file);
        $this->setDefaults();
    }
}
