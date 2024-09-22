<?php

namespace Rougin\Combustor;

/**
 * @package Combustor
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class AppTest extends Testcase
{
    /**
     * @var string
     */
    protected $error = 'Symfony\Component\Console\Exception\CommandNotFoundException';

    /**
     * @return void
     */
    public function test_combustor_yml_file()
    {
        $this->setExpectedException($this->error);

        $app = new Combustor(__DIR__ . '/Fixture');

        $app->make()->find('init');
    }
}
