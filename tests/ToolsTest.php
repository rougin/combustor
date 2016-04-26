<?php

namespace Rougin\Combustor;

use Rougin\Combustor\Common\Tools;

use PHPUnit_Framework_TestCase;

class ToolsTest extends PHPUnit_Framework_TestCase
{
    /**
     * Tests Tools::strip_table_schema.
     * 
     * @return void
     */
    public function testStripTableSchema()
    {
        $table = Tools::strip_table_schema('dbo.post');

        $this->assertEquals('post', $table);
    }
}
