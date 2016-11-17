<?php

namespace Rougin\Combustor;

class CombustorTest extends TestCase
{
    /**
     * @var array
     */
    protected $commands = [
        'Rougin\Combustor\Commands\MakeControllerCommand',
        'Rougin\Combustor\Commands\MakeLayoutCommand',
        'Rougin\Combustor\Commands\MakeModelCommand',
        'Rougin\Combustor\Commands\MakeViewCommand',
    ];

    /**
     * Tests if the initial commands exists.
     *
     * @return void
     */
    public function testCommandsExist()
    {
        $this->setDefaults();

        $application = new \Symfony\Component\Console\Application;

        foreach ($this->commands as $index => $command) {
            $application->add($this->buildCommand($command));
        }

        $this->assertTrue($application->has('make:layout'));
    }
}
