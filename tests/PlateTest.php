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

    const TYPE_VIEW = 2;

    /**
     * @var \Rougin\Combustor\Console
     */
    protected $app;

    /**
     * @var string
     */
    protected $path;

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
     * @return string
     */
    protected function getDoctrineView()
    {
        $create = $this->getTemplate('Doctrine/CreateView');

        $edit = $this->getTemplate('Doctrine/EditView');

        $index = $this->getTemplate('Doctrine/IndexView');

        return $create . "\n" . $edit . "\n" . $index;
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

    /**
     * @return string
     */
    protected function getWildfireView()
    {
        $create = $this->getTemplate('Wildfire/CreateView');

        $edit = $this->getTemplate('Wildfire/EditView');

        $index = $this->getTemplate('Wildfire/IndexView');

        return $create . "\n" . $edit . "\n" . $index;
    }

    /**
     * @return void
     */
    public function doSetUp()
    {
        $root = __DIR__ . '/Fixture';

        $this->path = $root;

        $this->app = new Console($root);
    }

    /**
     * @return void
     */
    public function test_doctrine_controller()
    {
        $test = $this->findCommand('create:controller');

        $input = array('table' => 'users');
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

        $input = array('table' => 'users');
        $input['--doctrine'] = true;

        $test->execute($input);

        $expected = $this->getDoctrineModel();

        $actual = $this->getActualModel('User');

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return void
     */
    public function test_doctrine_view()
    {
        $test = $this->findCommand('create:view');

        $input = array('table' => 'users');
        $input['--doctrine'] = true;

        $test->execute($input);

        $expected = $this->getDoctrineView();

        $actual = $this->getActualView('User');

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return void
     */
    public function test_wildfire_controller()
    {
        $test = $this->findCommand('create:controller');

        $input = array('table' => 'users');
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

        $input = array('table' => 'users');
        $input['--wildfire'] = true;

        $test->execute($input);

        $expected = $this->getWildfireModel();

        $actual = $this->getActualModel('User');

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return void
     */
    public function test_wildfire_view()
    {
        $test = $this->findCommand('create:view');

        $input = array('table' => 'users');
        $input['--wildfire'] = true;

        $test->execute($input);

        $expected = $this->getWildfireView();

        $actual = $this->getActualView('User');

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return void
     */
    public function test_with_layout()
    {
        $test = $this->findCommand('create:layout');

        $test->execute(array());

        $expected = $this->getLayoutView();

        $actual = $this->getActualLayout();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return void
     */
    public function test_with_packages_absent()
    {
        $test = $this->findCommand('create:controller');

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
        $test = $this->findCommand('create:view');

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

        $test = $this->findCommand('create:model');

        $input = array('table' => 'users');

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
        $path = $this->app->getAppPath();

        if ($type === self::TYPE_VIEW)
        {
            $path .= '/views';
        }

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
        $result = file_get_contents($file);

        return str_replace("\r\n", "\n", $result);
    }

    /**
     * @return string
     */
    protected function getActualLayout()
    {
        $name = 'layout';

        $header = $this->getActualFile($name . '/header', self::TYPE_VIEW);

        $footer = $this->getActualFile($name . '/footer', self::TYPE_VIEW);

        // Delete directory after getting the files ---
        $this->deleteView($name);
        // --------------------------------------------

        return $header . "\n" . $footer;
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
     * @param string $name
     *
     * @return string
     */
    protected function getActualView($name)
    {
        $name = strtolower(Inflector::plural($name));

        $create = $this->getActualFile($name . '/create', self::TYPE_VIEW);

        $edit = $this->getActualFile($name . '/edit', self::TYPE_VIEW);

        $index = $this->getActualFile($name . '/index', self::TYPE_VIEW);

        // Delete directory after getting the files ---
        $this->deleteView($name);
        // --------------------------------------------

        return $create . "\n" . $edit . "\n" . $index;
    }

    /**
     * @return string
     */
    protected function getLayoutView()
    {
        $header = $this->getTemplate('Layout/Header');

        $footer = $this->getTemplate('Layout/Footer');

        return $header . "\n" . $footer;
    }

    /**
     * @param string $name
     *
     * @return void
     */
    protected function deleteView($name)
    {
        $path = $this->app->getAppPath();

        $source = $path . '/views/' . $name;

        /** @var string[] */
        $files = glob($source . '/*.*');

        array_map('unlink', $files);

        rmdir($path . '/views/' . $name);
    }
}
