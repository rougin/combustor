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
        $name = Inflector::singular($table);

        $this->setName(ucfirst($name));
        $this->extendsTo('Rougin\Wildfire\Model');

        $this->addTrait('Rougin\Wildfire\Traits\PaginateTrait');
        $this->addTrait('Rougin\Wildfire\Traits\ValidateTrait');
        $this->addTrait('Rougin\Wildfire\Traits\WildfireTrait');
        $this->addTrait('Rougin\Wildfire\Traits\WritableTrait');

        $this->addClassProperty('db', 'CI_DB_query_builder')->asTag();
        $this->addClassProperty($name, ucfirst($name))->asTag();

        $ciLink = 'https://codeigniter.com/userguide3/libraries';

        $default = array();
        $default['page_query_string'] = true;
        $default['use_page_numbers'] = true;
        $default['query_string_segment'] = 'p';
        $default['reuse_query_string'] = true;

        $this->addArrayProperty('pagee', 'array<string, mixed>')
            ->withComment('Additional configuration to Pagination Class.')
            ->withLink($ciLink . '/pagination.html#customizing-the-pagination')
            ->withDefaultValue($default);

        $this->addArrayProperty('rules', 'array<string, string>[]')
            ->withComment('List of validation rules for Form Validation.')
            ->withLink($ciLink . '/form_validation.html#setting-rules-using-an-array');

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
