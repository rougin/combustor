<?php

namespace Rougin\Combustor\Commands;

use Symfony\Component\Console\Tester\CommandTester;

class MakeModelCommandTest extends \Rougin\Combustor\TestCase
{
    /**
     * @var string
     */
    protected $command = 'Rougin\Combustor\Commands\MakeModelCommand';

    /**
     * @var string
     */
    protected $table = 'post';

    /**
     * Tests if the command works.
     *
     * @return void
     */
    public function testCommandWithoutModel()
    {
        $this->setExpectedException('Rougin\Combustor\Exceptions\ModelNotFoundException');

        $tester = new CommandTester($this->buildCommand($this->command));

        $tester->execute([ 'table' => $this->table ]);
    }

    /**
     * Tests if the command works.
     *
     * @return void
     */
    public function testCommandWithCredoModel()
    {
        system('composer require rougin/credo:dev-master --dev');

        $this->runTestCommand();
    }

    /**
     * Tests if the command works.
     *
     * @return void
     */
    public function testCommandWithWildfireModel()
    {
        system('composer require rougin/wildfire:dev-master --dev');

        $this->runTestCommand();
    }

    /**
     * Tests if the command works.
     *
     * @return void
     */
    protected function runTestCommand()
    {
        $this->setDefaults();

        $tester = new CommandTester($this->buildCommand($this->command));

        $tester->execute([ 'table' => $this->table ]);

        $filename = ucfirst(singular($this->table));
        $filePath = $this->path . '/application/models/' . $filename . '.php';

        $this->assertFileExists($filePath);

        $this->setDefaults();
    }
}