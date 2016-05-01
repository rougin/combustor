<?php

namespace Rougin\Combustor;

use Rougin\Combustor\Common\Config;

use PHPUnit_Framework_TestCase;
use Rougin\Combustor\Fixture\CodeIgniterHelper;

class ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $appPath;

    /**
     * @var string
     */
    protected $configPath;

    /**
     * Sets up the config path.
     */
    public function setUp()
    {
        $this->appPath = __DIR__ . '/TestApp/application';
        $this->configPath = $this->appPath . '/config';
    }

    /**
     * Tests Config::get.
     * 
     * @return void
     */
    public function testGet()
    {
        CodeIgniterHelper::setDefaults($this->appPath);

        $config = new Config('config', $this->configPath);

        $this->assertFalse($config->get('enable_hooks', 96, 'boolean'));
        $this->assertEquals('index.php', $config->get('index_page', 31, 'string'));
        $this->assertEmpty($config->get('csrf_exclude_uris', 436, 'array'));

        CodeIgniterHelper::setDefaults($this->appPath);
    }

    /**
     * Tests Config::set.
     * 
     * @return void
     */
    public function testSet()
    {
        CodeIgniterHelper::setDefaults($this->appPath);

        $expected = [
            'http://localhost/',
            true,
            ['foo', 'bar']
        ];

        $config = new Config('config', $this->configPath);

        $config->set('base_url', 19, $expected[0], 'string');
        $config->set('csrf_protection', 431, $expected[1], 'boolean');
        $config->set('csrf_exclude_uris', 436, $expected[2], 'array');

        $config->save();

        $this->assertEquals($expected[0], $config->get('base_url', 19, 'string'));
        $this->assertEquals($expected[1], $config->get('csrf_protection', 431, 'boolean'));
        $this->assertEquals($expected[2], $config->get('csrf_exclude_uris', 436, 'array'));

        CodeIgniterHelper::setDefaults($this->appPath);
    }
}