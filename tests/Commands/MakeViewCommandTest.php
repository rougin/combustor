<?php

namespace Rougin\Combustor\Commands;

use Symfony\Component\Console\Tester\CommandTester;

class MakeViewCommandTest extends \Rougin\Combustor\TestCase
{
    /**
     * @var string
     */
    protected $command = 'Rougin\Combustor\Commands\MakeViewCommand';

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

        $filename = plural($this->table);

        $create = $this->path . '/application/views/' . $filename . '/create.php';
        $edit   = $this->path . '/application/views/' . $filename . '/edit.php';
        $index  = $this->path . '/application/views/' . $filename . '/index.php';
        $show   = $this->path . '/application/views/' . $filename . '/show.php';

        $this->assertTrue(file_exists($create) && file_exists($edit) && file_exists($index) && file_exists($show));

        $this->setDefaults();
    }
}
