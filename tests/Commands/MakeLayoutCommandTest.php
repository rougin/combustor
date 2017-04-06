<?php

namespace Rougin\Combustor\Commands;

use Symfony\Component\Console\Tester\CommandTester;

class MakeLayoutCommandTest extends \Rougin\Combustor\TestCase
{
    /**
     * @var string
     */
    protected $command = 'Rougin\Combustor\Commands\MakeLayoutCommand';

    /**
     * Tests if the command works.
     *
     * @return void
     */
    public function testCommand()
    {
        $this->setDefaults();

        (new CommandTester($this->buildCommand($this->command)))->execute([]);

        $header = $this->path . '/application/views/layout/header.php';
        $footer = $this->path . '/application/views/layout/footer.php';

        $this->assertTrue(file_exists($header) && file_exists($footer));

        $this->setDefaults();
    }
}
