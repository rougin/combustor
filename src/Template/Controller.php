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
     * @param string $table
     */
    public function __construct($table)
    {
        $this->init($table);
    }

    /**
     * Configures the current class.
     *
     * @param string $table
     *
     * @return void
     */
    public function init($table)
    {
        $this->setPackage('Codeigniter');

        $name = Inflector::plural($table);
        $this->setName(ucfirst($name));

        $extends = 'Rougin\SparkPlug\Controller';
        $this->extendsTo($extends);

        $method = new Method('__construct');
        $method->setCodeLine(function ($lines)
        {
            $lines[] = 'parent::__construct();';

            return $lines;
        });
        $this->addMethod($method);

        $method = new Method('create');
        $method->setReturn('void');
        $this->addMethod($method);

        $method = new Method('delete');
        $method->addIntegerArgument('id');
        $method->setReturn('void');
        $this->addMethod($method);

        $method = new Method('edit');
        $method->addIntegerArgument('id');
        $method->setReturn('void');
        $this->addMethod($method);

        $method = new Method('index');
        $method->setReturn('void');
        $this->addMethod($method);

        $method = new Method('show');
        $method->addIntegerArgument('id');
        $method->setReturn('void');
        $this->addMethod($method);
    }
}
