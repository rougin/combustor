<?php

namespace Rougin\Combustor\Commands;

use Symfony\Component\Console\Tester\CommandTester;

class MakeControllerCommandTest extends \Rougin\Combustor\TestCase
{
    /**
     * @var string
     */
    protected $command = 'Rougin\Combustor\Commands\MakeControllerCommand';

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

        $tester = new CommandTester($this->buildCommand($this->command));

        $tester->execute([ 'table' => $this->table ]);

        $filename = ucfirst(plural($this->table));
        $filePath = $this->path . '/application/controllers/' . $filename . '.php';

        $this->assertFileExists($filePath);

        $this->setDefaults();
    }
}
