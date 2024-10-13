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

    const TYPE_REPOSITORY = 2;

    const TYPE_VIEW = 3;

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

        $test->execute(array('--force' => true));

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

        $input = array('--force' => true);
        $input['--bootstrap'] = true;

        $test->execute($input);

        $expected = $this->getLayoutView(self::VIEW_STYLED);

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
        $input['--force'] = true;
        $input['--doctrine'] = true;

        $test->execute($input);

        $expected = $this->getDoctrineCtrl('Users');

        $actual = $this->getActualCtrl('Users');

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return void
     */
    public function test_doctrine_controller_with_foreigns()
    {
        $test = $this->findCommand('create:controller');

        $input = array('table' => 'posts');
        $input['--force'] = true;
        $input['--doctrine'] = true;

        $test->execute($input);

        $expected = $this->getDoctrineCtrl('Posts');

        $actual = $this->getActualCtrl('Posts');

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return void
     */
    public function test_doctrine_model()
    {
        $test = $this->findCommand('create:model');

        $input = array('table' => 'users');
        $input['--force'] = true;
        $input['--doctrine'] = true;

        $test->execute($input);

        $expected = $this->getDoctrineModel('User');

        $actual = $this->getActualModel('User');

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return void
     */
    public function test_doctrine_model_with_foreigns()
    {
        $test = $this->findCommand('create:model');

        $input = array('table' => 'posts');
        $input['--force'] = true;
        $input['--doctrine'] = true;

        $test->execute($input);

        $expected = $this->getDoctrineModel('Post');

        $actual = $this->getActualModel('Post');

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return void
     */
    public function test_doctrine_repository()
    {
        $test = $this->findCommand('create:repository');

        $input = array('table' => 'users');
        $input['--force'] = true;

        $test->execute($input);

        $expected = $this->getDoctrineRepo('User');

        $actual = $this->getActualRepo('User');

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return void
     */
    public function test_doctrine_view()
    {
        $test = $this->findCommand('create:view');

        $input = array('table' => 'users');
        $input['--force'] = true;
        $input['--doctrine'] = true;

        $test->execute($input);

        $expected = $this->getDoctrineView('Users');

        $actual = $this->getActualView('Users');

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
        $input['--force'] = true;

        $test->execute($input);

        $expected = $this->getDoctrineView('Users', self::VIEW_STYLED);

        $actual = $this->getActualView('Users');

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return void
     */
    public function test_doctrine_view_with_foreigns()
    {
        $test = $this->findCommand('create:view');

        $input = array('table' => 'posts');
        $input['--force'] = true;
        $input['--doctrine'] = true;

        $test->execute($input);

        $expected = $this->getDoctrineView('Posts');

        $actual = $this->getActualView('Posts');

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return void
     */
    public function test_empty_controller()
    {
        $test = $this->findCommand('create:controller');

        $input = array('table' => 'users');
        $input['--empty'] = true;
        $input['--wildfire'] = true;
        $input['--force'] = true;

        $test->execute($input);

        $expected = $this->getEmptyCtrl();

        $actual = $this->getActualCtrl('Users');

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return void
     */
    public function test_empty_model()
    {
        $test = $this->findCommand('create:model');

        $input = array('table' => 'users');
        $input['--empty'] = true;
        $input['--wildfire'] = true;
        $input['--force'] = true;

        $test->execute($input);

        $expected = $this->getEmptyModel();

        $actual = $this->getActualModel('User');

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return void
     */
    public function test_existing_controller()
    {
        $test = $this->findCommand('create:controller');

        $input = array('table' => 'users');
        $input['--doctrine'] = true;

        $test->execute($input);

        $expected = '[FAIL] "Users" already exists. Use --force to overwrite the file.';

        $actual = $this->getActualDisplay($test);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return void
     */
    public function test_existing_layout()
    {
        // Run the command to create its directory ---
        $old = $this->findCommand('create:layout');

        $old->execute(array());
        // -------------------------------------------

        $test = $this->findCommand('create:layout');

        $test->execute(array());

        $expected = '[FAIL] "header.php", "footer.php" already exists. Use --force to overwrite them.';

        $actual = $this->getActualDisplay($test);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return void
     */
    public function test_existing_model()
    {
        $test = $this->findCommand('create:model');

        $input = array('table' => 'users');
        $input['--doctrine'] = true;

        $test->execute($input);

        $expected = '[FAIL] "User" already exists. Use --force to overwrite the file.';

        $actual = $this->getActualDisplay($test);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return void
     */
    public function test_existing_repository()
    {
        $input = array('table' => 'users');

        // Run the command to create its directory ---
        $old = $this->findCommand('create:repository');

        $old->execute($input);
        // -------------------------------------------

        $test = $this->findCommand('create:repository');

        $test->execute($input);

        $expected = '[FAIL] "User_repository" already exists. Use --force to overwrite the file.';

        $actual = $this->getActualDisplay($test);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return void
     */
    public function test_existing_views()
    {
        $input = array('table' => 'users');
        $input['--doctrine'] = true;

        // Run the command to create the directory ---
        $old = $this->findCommand('create:views');

        $old->execute($input);
        // -------------------------------------------

        $test = $this->findCommand('create:views');

        $test->execute($input);

        $expected = '[FAIL] "users" directory already exists. Use --force to overwrite the directory.';

        $actual = $this->getActualDisplay($test);

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
        $input['--force'] = true;

        $test->execute($input);

        // Return the controller, model, and views --------
        $route = $this->getDoctrineCtrl('Users');

        $model = $this->getDoctrineModel('User');

        $repo = $this->getDoctrineRepo('User');

        $views = $this->getDoctrineView('Users', self::VIEW_STYLED);

        $expected = $route . "\n" . $model . "\n" . $views;

        $expected = $expected . "\n" . $repo;
        // ------------------------------------------------

        // Return the actual results --------------------
        $route = $this->getActualCtrl('Users');

        $model = $this->getActualModel('User');

        $repo = $this->getActualRepo('User');

        $views = $this->getActualView('Users');

        $actual = $route . "\n" . $model . "\n" . $views;

        $actual = $actual . "\n" . $repo;
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
        $input['--force'] = true;
        $input['--wildfire'] = true;

        $test->execute($input);

        $expected = $this->getWildfireCtrl('Users');

        $actual = $this->getActualCtrl('Users');

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return void
     */
    public function test_wildfire_controller_with_foreigns()
    {
        $test = $this->findCommand('create:controller');

        $input = array('table' => 'posts');
        $input['--force'] = true;
        $input['--wildfire'] = true;

        $test->execute($input);

        $expected = $this->getWildfireCtrl('Posts');

        $actual = $this->getActualCtrl('Posts');

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return void
     */
    public function test_wildfire_model()
    {
        $test = $this->findCommand('create:model');

        $input = array('table' => 'users');
        $input['--force'] = true;
        $input['--wildfire'] = true;

        $test->execute($input);

        $expected = $this->getWildfireModel('User');

        $actual = $this->getActualModel('User');

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return void
     */
    public function test_wildfire_model_with_foreigns()
    {
        $test = $this->findCommand('create:model');

        $input = array('table' => 'posts');
        $input['--force'] = true;
        $input['--wildfire'] = true;

        $test->execute($input);

        $expected = $this->getWildfireModel('Post');

        $actual = $this->getActualModel('Post');

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return void
     */
    public function test_wildfire_view()
    {
        $test = $this->findCommand('create:view');

        $input = array('table' => 'users');
        $input['--force'] = true;
        $input['--wildfire'] = true;

        $test->execute($input);

        $expected = $this->getWildfireView('Users');

        $actual = $this->getActualView('Users');

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return void
     */
    public function test_wildfire_view_with_bootstrap()
    {
        $test = $this->findCommand('create:view');

        $input = array('table' => 'users');
        $input['--bootstrap'] = true;
        $input['--force'] = true;
        $input['--wildfire'] = true;

        $test->execute($input);

        $expected = $this->getWildfireView('Users', self::VIEW_STYLED);

        $actual = $this->getActualView('Users');

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return void
     */
    public function test_wildfire_view_with_foreigns()
    {
        $test = $this->findCommand('create:view');

        $input = array('table' => 'posts');
        $input['--force'] = true;
        $input['--wildfire'] = true;

        $test->execute($input);

        $expected = $this->getWildfireView('Posts');

        $actual = $this->getActualView('Posts');

        $this->assertEquals($expected, $actual);
    }

    /**
     * @param string $name
     *
     * @return void
     */
    protected function deleteDir($name)
    {
        $path = $this->app->getAppPath();

        $source = $path . '/' . $name;

        /** @var string[] */
        $files = glob($source . '/*.*');

        array_map('unlink', $files);

        rmdir($path . '/' . $name);
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

        if ($type === self::TYPE_REPOSITORY)
        {
            $path .= '/repositories';

            $name = $name . '_repository';
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
        // $this->deleteDir('views/layout');
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
    protected function getActualRepo($name)
    {
        $file = $this->getActualFile($name, self::TYPE_REPOSITORY);

        // Delete directory after getting the files ---
        $this->deleteDir('repositories');
        // --------------------------------------------

        return $file;
    }

    /**
     * @param string $name
     *
     * @return string
     */
    protected function getActualView($name)
    {
        $name = strtolower($name);

        $create = $this->getActualFile($name . '/create', self::TYPE_VIEW);

        $edit = $this->getActualFile($name . '/edit', self::TYPE_VIEW);

        $index = $this->getActualFile($name . '/index', self::TYPE_VIEW);

        // Delete directory after getting the files ---
        $this->deleteDir('views/' . $name);
        // --------------------------------------------

        return $create . "\n" . $edit . "\n" . $index;
    }

    /**
     * @param string $name
     *
     * @return string
     */
    protected function getDoctrineCtrl($name)
    {
        return $this->getTemplate('Doctrine/Routes/' . $name);
    }

    /**
     * @param string $name
     *
     * @return string
     */
    protected function getDoctrineModel($name)
    {
        return $this->getTemplate('Doctrine/Models/' . $name);
    }

    /**
     * @param string $name
     *
     * @return string
     */
    protected function getDoctrineRepo($name)
    {
        return $this->getTemplate('Doctrine/Repos/' . $name);
    }

    /**
     * @param string  $name
     * @param integer $type
     *
     * @return string
     */
    protected function getDoctrineView($name, $type = self::VIEW_COMMON)
    {
        $type = $type === self::VIEW_STYLED ? 'Styled' : 'Common';

        $create = $this->getTemplate('Doctrine/Plates/' . $name . '/' . $type . '/CreateView');

        $edit = $this->getTemplate('Doctrine/Plates/' . $name . '/' . $type . '/EditView');

        $index = $this->getTemplate('Doctrine/Plates/' . $name . '/' . $type . '/IndexView');

        return $create . "\n" . $edit . "\n" . $index;
    }

    /**
     * @return string
     */
    protected function getEmptyCtrl()
    {
        return $this->getTemplate('Empty/Controller');
    }

    /**
     * @return string
     */
    protected function getEmptyModel()
    {
        return $this->getTemplate('Empty/Model');
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
     * @param string $name
     *
     * @return string
     */
    protected function getWildfireCtrl($name)
    {
        return $this->getTemplate('Wildfire/Routes/' . $name);
    }

    /**
     * @param string $name
     *
     * @return string
     */
    protected function getWildfireModel($name)
    {
        return $this->getTemplate('Wildfire/Models/' . $name);
    }

    /**
     * @param string  $name
     * @param integer $type
     *
     * @return string
     */
    protected function getWildfireView($name, $type = self::VIEW_COMMON)
    {
        $type = $type === self::VIEW_STYLED ? 'Styled' : 'Common';

        $create = $this->getTemplate('Wildfire/Plates/' . $name . '/' . $type . '/CreateView');

        $edit = $this->getTemplate('Wildfire/Plates/' . $name . '/' . $type . '/EditView');

        $index = $this->getTemplate('Wildfire/Plates/' . $name . '/' . $type . '/IndexView');

        return $create . "\n" . $edit . "\n" . $index;
    }
}
