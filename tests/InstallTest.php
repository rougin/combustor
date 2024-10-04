<?php

namespace Rougin\Combustor;

use Symfony\Component\Console\Tester\CommandTester;

/**
 * @package Combustor
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class InstallTest extends Testcase
{
    /**
     * @return void
     */
    public function test_install_doctrine()
    {
        $test = $this->findCommand('install:doctrine');

        $test->execute(array());

        $expected = '[PASS] Doctrine installed successfully!';

        $actual = $this->getActualDisplay($test);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return void
     */
    public function test_install_wildfire()
    {
        $test = $this->findCommand('install:wildfire');

        $test->execute(array());

        $expected = '[PASS] Wildfire installed successfully!';

        $actual = $this->getActualDisplay($test);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @depends test_install_doctrine
     *
     * @return void
     */
    public function test_remove_doctrine()
    {
        // Mock class of specified packages -----
        $test = 'Rougin\Combustor\Inflector';
        class_alias($test, 'Rougin\Credo\Credo');
        // --------------------------------------

        $test = $this->findCommand('remove:doctrine');

        $test->execute(array());

        $expected = '[PASS] Doctrine removed successfully!';

        $actual = $this->getActualDisplay($test);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @depends test_install_wildfire
     *
     * @return void
     */
    public function test_remove_wildfire()
    {
        // Mock class of specified packages -----------
        $test = 'Rougin\Combustor\Inflector';
        class_alias($test, 'Rougin\Wildfire\Wildfire');
        // --------------------------------------------

        $test = $this->findCommand('remove:wildfire');

        $test->execute(array());

        $expected = '[PASS] Wildfire removed successfully!';

        $actual = $this->getActualDisplay($test);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @param string $name
     *
     * @return \Symfony\Component\Console\Tester\CommandTester
     */
    protected function findCommand($name)
    {
        system('composer update');

        $app = new Console(__DIR__ . '/Fixture');

        $command = $app->make()->find($name);

        return new CommandTester($command);
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
