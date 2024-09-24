<?php

namespace Rougin\Combustor;

use Symfony\Component\Console\Tester\CommandTester;

/**
 * @package Combustor
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class PlateTest extends Testcase
{
    const TYPE_CONTROLLER = 0;

    const TYPE_MODEL = 1;

    /**
     * @var \Rougin\Blueprint\Blueprint
     */
    protected $app;

    /**
     * @var string
     */
    protected $path;

    /**
     * @return void
     */
    public function doSetUp()
    {
        $root = __DIR__ . '/Fixture/Sample';

        $this->path = $root;

        $this->app = new Combustor($root);
    }

    /**
     * @return void
     */
    public function test_doctrine_controller()
    {
        $test = $this->findCommand('create:controller');

        $input = array('name' => 'users');
        $input['--doctrine'] = true;

        $test->execute($input);

        $expected = $this->getTemplate('DoctrineController');

        $actual = $this->getActualFile('Users');

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return void
     */
    public function test_wildfire_controller()
    {
        $test = $this->findCommand('create:controller');

        $input = array('name' => 'users');
        $input['--wildfire'] = true;

        $test->execute($input);

        $expected = $this->getTemplate('WildfireController');

        $actual = $this->getActualFile('Users');

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return void
     */
    // public function test_with_both_packages_installed()
    // {
    //     system('composer require rougin/credo:dev-master rougin/wildfire:dev-master');

    //     $test = $this->findCommand('create:controller');

    //     $input = array('name' => 'users');

    //     $test->execute($input);

    //     $expected = '[FAIL] Both "rougin/credo" and "rougin/wildfire" are installed. Kindly select --doctrine or --wildfire first.';

    //     $actual = $this->getActualDisplay($test);

    //     $this->assertEquals($expected, $actual);

    //     system('composer remove rougin/credo:dev-master rougin/wildfire:dev-master');
    // }

    /**
     * @return void
     */
    public function test_without_package_installed()
    {
        $test = $this->findCommand('create:controller');

        $input = array('name' => 'users');

        $test->execute($input);

        $expected = '[FAIL] Both "rougin/credo" and "rougin/wildfire" are not installed. Kindly "rougin/credo" or "rougin/wildfire" first.';

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
        return new CommandTester($this->app->make()->find($name));
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

    /**
     * @param string  $name
     * @param integer $type
     *
     * @return string
     */
    protected function getActualFile($name, $type = self::TYPE_CONTROLLER)
    {
        $path = $this->path;

        if ($type === self::TYPE_CONTROLLER)
        {
            $path .= '/controllers';
        }

        if ($type === self::TYPE_MODEL)
        {
            $path .= '/models';
        }

        $file = $path . '/' . $name . '.php';

        /** @var string */
        $file = file_get_contents($file);

        return str_replace("\r\n", "\n", $file);
    }

    /**
     * @param string $name
     *
     * @return string
     */
    protected function getTemplate($name)
    {
        $path = __DIR__ . '/Fixture/Plates/' . $name . '.php';

        /** @var string */
        $file = file_get_contents($path);

        return str_replace("\r\n", "\n", $file);
    }
}
