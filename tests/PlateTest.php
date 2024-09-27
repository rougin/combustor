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

        $this->app = new Console($root);
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

        $expected = $this->getDoctrineCtrl();

        $actual = $this->getActualCtrl('Users');

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return void
     */
    public function test_doctrine_model()
    {
        $test = $this->findCommand('create:model');

        $input = array('name' => 'users');
        $input['--doctrine'] = true;

        $test->execute($input);

        $expected = $this->getDoctrineModel();

        $actual = $this->getActualModel('User');

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

        $expected = $this->getWildfireCtrl();

        $actual = $this->getActualCtrl('Users');

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return void
     */
    public function test_wildfire_model()
    {
        $test = $this->findCommand('create:model');

        $input = array('name' => 'users');
        $input['--wildfire'] = true;

        $test->execute($input);

        $expected = $this->getWildfireModel();

        $actual = $this->getActualModel('User');

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return void
     */
    public function test_with_packages_absent()
    {
        $test = $this->findCommand('create:controller');

        $input = array('name' => 'users');

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

        $test = $this->findCommand('create:controller');

        $input = array('name' => 'users');

        $test->execute($input);

        $expected = '[FAIL] Both "rougin/credo" and "rougin/wildfire" are installed. Kindly select --doctrine or --wildfire first.';

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
     * @param string $name
     *
     * @return string
     */
    protected function getActualCtrl($name)
    {
        return $this->getActualFile($name, self::TYPE_CONTROLLER);
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
    protected function getActualModel($name)
    {
        return $this->getActualFile($name, self::TYPE_MODEL);
    }

    /**
     * @return string
     */
    protected function getDoctrineCtrl()
    {
        return $this->getTemplate('Doctrine/Controller');
    }

    /**
     * @return string
     */
    protected function getDoctrineModel()
    {
        return $this->getTemplate('Doctrine/Model');
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

    /**
     * @return string
     */
    protected function getWildfireCtrl()
    {
        return $this->getTemplate('Wildfire/Controller');
    }

    /**
     * @return string
     */
    protected function getWildfireModel()
    {
        return $this->getTemplate('Wildfire/Model');
    }
}
