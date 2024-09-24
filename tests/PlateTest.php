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
        $command = $this->findCommand('create:controller');

        $input = array('name' => 'users');
        $input['--doctrine'] = true;

        $command->execute($input);

        $expected = $this->getTemplate('DoctrineController');

        $actual = $this->getActual('Users');

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return void
     */
    public function test_wildfire_controller()
    {
        $command = $this->findCommand('create:controller');

        $input = array('name' => 'users');
        $input['--wildfire'] = true;

        $command->execute($input);

        $expected = $this->getTemplate('WildfireController');

        $actual = $this->getActual('Users');

        $this->assertEquals($expected, $actual);
    }

    /**
     * @param string  $name
     * @param integer $type
     *
     * @return string
     */
    protected function getActual($name, $type = self::TYPE_CONTROLLER)
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

    /**
     * @param string $name
     *
     * @return \Symfony\Component\Console\Tester\CommandTester
     */
    protected function findCommand($name)
    {
        return new CommandTester($this->app->make()->find($name));
    }
}
