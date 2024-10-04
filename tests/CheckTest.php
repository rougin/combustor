<?php

namespace Rougin\Combustor;

use Symfony\Component\Console\Tester\CommandTester;

/**
 * @runTestsInSeparateProcesses
 *
 * @package Combustor
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class CheckTest extends Testcase
{
    /**
     * @return void
     */
    public function test_with_packages_absent()
    {
        $app = new Console(__DIR__ . '/Fixture');

        $command = $app->make()->find('create:controller');

        $test = new CommandTester($command);

        $input = array('table' => 'users');

        $test->execute($input);

        $expected = '[FAIL] Both "rougin/credo" and "rougin/wildfire" are not installed. Kindly "rougin/credo" or "rougin/wildfire" first.';

        $actual = $this->getActualDisplay($test);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return void
     */
    public function test_with_packages_absent_as_scaffold()
    {
        $app = new Console(__DIR__ . '/Fixture');

        $command = $app->make()->find('create:scaffold');

        $test = new CommandTester($command);

        $input = array('table' => 'users');

        $test->execute($input);

        $expected = '[FAIL] Both "rougin/credo" and "rougin/wildfire" are not installed. Kindly "rougin/credo" or "rougin/wildfire" first.';

        $actual = $this->getActualDisplay($test);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return void
     */
    public function test_with_packages_absent_view()
    {
        $app = new Console(__DIR__ . '/Fixture');

        $command = $app->make()->find('create:view');

        $test = new CommandTester($command);

        $input = array('table' => 'users');

        $test->execute($input);

        $expected = '[FAIL] Both "rougin/credo" and "rougin/wildfire" are not installed. Kindly "rougin/credo" or "rougin/wildfire" first.';

        $actual = $this->getActualDisplay($test);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return void
     */
    public function test_with_packages_present()
    {
        // Mock class of specified packages ---
        $test = 'Rougin\Combustor\Inflector';

        $doctrine = 'Rougin\Credo\Credo';
        class_alias($test, $doctrine);

        $wildfire = 'Rougin\Wildfire\Wildfire';
        class_alias($test, $wildfire);
        // ------------------------------------

        $app = new Console(__DIR__ . '/Fixture');

        $command = $app->make()->find('create:model');

        $test = new CommandTester($command);

        $input = array('table' => 'users');

        $test->execute($input);

        $expected = '[FAIL] Both "rougin/credo" and "rougin/wildfire" are installed. Kindly select --doctrine or --wildfire first.';

        $actual = $this->getActualDisplay($test);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @param \Symfony\Component\Console\Tester\CommandTester $tester
     *
     * @return string
     */
    protected function getActualDisplay(CommandTester $tester)
    {
        $actual = $tester->getDisplay();

        $actual = str_replace("\r\n", '', $actual);

        return str_replace("\n", '', $actual);
    }
}
