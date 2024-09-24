<?php

namespace Rougin\Combustor\Template\Wildfire;

use Rougin\Classidy\Classidy;
use Rougin\Classidy\Method;
use Rougin\Combustor\Inflector;

/**
 * @package Combustor
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class Model extends Classidy
{
    const TYPE_WILDFIRE = 0;

    const TYPE_DOCTRINE = 1;

    /**
     * @var integer
     */
    protected $type;

    /**
     * @param string  $table
     * @param integer $type
     */
    public function __construct($table, $type)
    {
        $this->type = $type;

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
        $name = Inflector::plural($table);

        $model = Inflector::singular($table);

        /** @var class-string */
        $class = ucfirst($model);

        $ctrl = ucfirst($name);

        $this->setName('User');
        $this->extendsTo('Rougin\Wildfire\Model');

        $this->addTrait('Rougin\Wildfire\Traits\PaginateTrait');
        $this->addTrait('Rougin\Wildfire\Traits\ValidateTrait');
        $this->addTrait('Rougin\Wildfire\Traits\WildfireTrait');
        $this->addTrait('Rougin\Wildfire\Traits\WritableTrait');

        $this->addClassProperty('db', 'CI_DB_query_builder')->asTag();
        $this->addClassProperty($model, $class)->asTag();

        $default = array();
        $default['page_query_string'] = true;
        $default['use_page_numbers'] = true;
        $default['query_string_segment'] = 'p';
        $default['reuse_query_string'] = true;

        $this->addArrayProperty('pagee', 'array<string, mixed>')
            ->withComment('Additional configuration to Pagination Class.')
            ->withLink('https://codeigniter.com/userguide3/libraries/pagination.html?highlight=pagination#customizing-the-pagination')
            ->withDefaultValue($default);

        $this->addArrayProperty('rules', 'array<string, string>[]')
            ->withComment('List of validation rules.')
            ->withLink('https://codeigniter.com/userguide3/libraries/form_validation.html#setting-rules-using-an-array');

        $this->addStringProperty('table')
            ->withComment('The table associated with the model.')
            ->withDefaultValue('users');

        $method = new Method('exists');
        $method->setReturn('boolean');
        $method->addArrayArgument('data', 'array<string, mixed>');
        $method->addIntegerArgument('id', true);
        $method->setCodeLine(function ($lines)
        {
            $lines[] = '// Specify logic here if applicable ---';
            $lines[] = '// ------------------------------------';
            $lines[] = '';
            $lines[] = 'return false;';

            return $lines;
        });
        $this->addMethod($method);

        $method = new Method('input');
        $method->asProtected();
        $method->setReturn('array<string, mixed>');
        $method->addArrayArgument('data', 'array<string, mixed>');
        $method->addIntegerArgument('id', true);
        $method->setCodeLine(function ($lines)
        {
            $lines[] = '$input = array();';
            $lines[] = '';
            $lines[] = '// List editable fields from table ---';
            $lines[] = '// -----------------------------------';
            $lines[] = '';
            $lines[] = 'return $input;';

            return $lines;
        });
        $this->addMethod($method);
    }
}
