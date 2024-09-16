<?php

namespace Rougin\Combustor\Template;

use Rougin\Classidy\Classidy;
use Rougin\Classidy\Method;
use Rougin\Combustor\Inflector;

/**
 * @package Combustor
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class Controller extends Classidy
{
    /**
     * @var string
     */
    protected $table;

    /**
     * @param string $table
     */
    public function __construct($table)
    {
        $this->table = $table;
    }

    /**
     * Configures the current class.
     *
     * @return void
     */
    public function init()
    {
        $this->setPackage('Codeigniter');

        $name = Inflector::plural($this->table);
        $this->setName(ucfirst($name));

        $extends = 'Rougin\SparkPlug\Controller';
        $this->extendsTo($extends);

        $method = new Method('__construct');
        $method->setCodeLine(function ($lines)
        {
            $lines[] = 'parent::__construct();';
            $lines[] = '';

            return $lines;
        });

        $this->addMethod($method);
    }
}
