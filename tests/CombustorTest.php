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

        $application = \Rougin\Combustor\Combustor::boot()->run(true);

        $this->assertTrue($application->has('make:layout'));
    }
}
