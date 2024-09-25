<?php

namespace Rougin\Combustor\Template\Doctrine;

use Rougin\Classidy\Classidy;
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
     * @param string $table
     *
     * @return void
     */
    public function init($table)
    {
        $name = Inflector::singular($table);

        $this->setName(ucfirst($name));
        $this->extendsTo('Rougin\Credo\Model');

        $this->addTrait('Rougin\Credo\Traits\PaginateTrait');
        $this->addTrait('Rougin\Credo\Traits\ValidateTrait');

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
    }
}
