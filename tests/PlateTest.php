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

    const VIEW_COMMON = 0;

    const VIEW_STYLED = 1;

    /**
     * @var \Rougin\Combustor\Console
     */
    protected $app;

    /**
     * @return void
     */
    public function doSetUp()
    {
        $this->app = new Console(__DIR__ . '/Fixture');
    }

    /**
     * @return void
     */
    public function test_creating_layout()
    {
        $test = $this->findCommand('create:layout');

        $test->execute(array());

        $type = self::VIEW_COMMON;

        $expected = $this->getLayoutView($type);

        $actual = $this->getActualLayout();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return void
     */
    public function test_creating_layout_with_bootstrap()
    {
        $test = $this->findCommand('create:layout');

        $test->execute(array('--bootstrap' => true));

        $type = self::VIEW_STYLED;

        $expected = $this->getLayoutView($type);

        $actual = $this->getActualLayout();

        $this->assertEquals($expected, $actual);
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
    public function test_doctrine_view_with_bootstrap()
    {
        $test = $this->findCommand('create:view');

        $input = array('table' => 'users');
        $input['--doctrine'] = true;
        $input['--bootstrap'] = true;

        $test->execute($input);

        $expected = $this->getDoctrineView(self::VIEW_STYLED);

        $actual = $this->getActualView('User');

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return void
     */
    public function test_scaffold_on_doctrine()
    {
        $test = $this->findCommand('create:scaffold');

        $input = array('table' => 'users');
        $input['--doctrine'] = true;
        $input['--bootstrap'] = true;

        $test->execute($input);

        // Return the controller, model, and views --------
        $route = $this->getDoctrineCtrl();

        $model = $this->getDoctrineModel();

        $views = $this->getDoctrineView(self::VIEW_STYLED);

        $expected = $route . "\n" . $model . "\n" . $views;
        // ------------------------------------------------

        // Return the actual results --------------------
        $route = $this->getActualCtrl('Users');

        $model = $this->getActualModel('User');

        $views = $this->getActualView('User');

        $actual = $route . "\n" . $model . "\n" . $views;
        // ----------------------------------------------

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
    public function test_wildfire_view_with_bootstrap()
    {
        $test = $this->findCommand('create:view');

        $input = array('table' => 'users');
        $input['--wildfire'] = true;
        $input['--bootstrap'] = true;

        $test->execute($input);

        $expected = $this->getWildfireView(self::VIEW_STYLED);

        $actual = $this->getActualView('User');

        $this->assertEquals($expected, $actual);
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
     * @param integer $type
     *
     * @return string
     */
    protected function getDoctrineView($type = self::VIEW_COMMON)
    {
        $name = 'Common';

        if ($type === self::VIEW_STYLED)
        {
            $name = 'Styled';
        }

        $create = $this->getTemplate('Doctrine/' . $name . '/CreateView');

        $edit = $this->getTemplate('Doctrine/' . $name . '/EditView');

        $index = $this->getTemplate('Doctrine/' . $name . '/IndexView');

        return $create . "\n" . $edit . "\n" . $index;
    }

    /**
     * @param integer $type
     *
     * @return string
     */
    protected function getLayoutView($type = self::VIEW_COMMON)
    {
        $name = 'Common';

        if ($type === self::VIEW_STYLED)
        {
            $name = 'Styled';
        }

        $header = $this->getTemplate('Layout/' . $name . 'Header');

        $footer = $this->getTemplate('Layout/' . $name . 'Footer');

        return $header . "\n" . $footer;
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
     * @param integer $type
     *
     * @return string
     */
    protected function getWildfireView($type = self::VIEW_COMMON)
    {
        $name = 'Common';

        if ($type === self::VIEW_STYLED)
        {
            $name = 'Styled';
        }

        $create = $this->getTemplate('Wildfire/' . $name . '/CreateView');

        $edit = $this->getTemplate('Wildfire/' . $name . '/EditView');

        $index = $this->getTemplate('Wildfire/' . $name . '/IndexView');

        return $create . "\n" . $edit . "\n" . $index;
    }
}
