<?php

namespace Rougin\Combustor;

class CombustorTest extends TestCase
{
    /**
     * Tests if the initial commands exists.
     *
     * @return void
     */
    public function testCommandsExist()
    {
        $this->setDefaults();

        $path = __DIR__ . '/Application';

        $application = \Rougin\Combustor\Combustor::boot('combustor.yml', null, $path)->run(true);

        $this->assertTrue($application->has('make:layout'));
    }
}
