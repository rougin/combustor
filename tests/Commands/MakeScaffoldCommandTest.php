<?php

namespace Rougin\Combustor\Commands;

use Symfony\Component\Console\Tester\CommandTester;

class MakeScaffoldCommandTest extends \Rougin\Combustor\TestCase
{
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

        $application = $this->getApplication();

        $command = $application->find('make:scaffold');
        $tester  = new CommandTester($command);

        $tester->execute([ 'table' => $this->table ]);

        $controller = $this->path . '/application/controllers/' . ucfirst(plural($this->table)) . '.php';
        $model      = $this->path . '/application/models/' . ucfirst(singular($this->table)) . '.php';

        $create = $this->path . '/application/views/' . plural($this->table) . '/create.php';
        $edit   = $this->path . '/application/views/' . plural($this->table) . '/edit.php';
        $index  = $this->path . '/application/views/' . plural($this->table) . '/index.php';
        $show   = $this->path . '/application/views/' . plural($this->table) . '/show.php';

        $filesExists = file_exists($controller) && file_exists($model) && file_exists($create) && file_exists($edit) && file_exists($index) && file_exists($show);

        $this->assertTrue($filesExists);

        $this->setDefaults();
    }
}
