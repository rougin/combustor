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
     * @var \Rougin\Describe\Column[]
     */
    protected $cols;

    /**
     * @param string                    $table
     * @param \Rougin\Describe\Column[] $cols
     */
    public function __construct($table, $cols)
    {
        $this->cols = $cols;

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

        $this->setProperties();

        $link = 'https://codeigniter.com/userguide3/libraries';

        $this->setPagee($link);

        $this->setRules($link);

        $this->setExistsMethod();

        $this->setInputMethod();
    }

    /**
     * @return void
     */
    protected function setExistsMethod()
    {
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
    }

    /**
     * @return void
     */
    protected function setInputMethod()
    {
        $method = new Method('input');

        $method->asProtected();

        $method->setReturn('array<string, mixed>');

        $method->addArrayArgument('data', 'array<string, mixed>');

        $method->addIntegerArgument('id', true);

        $cols = $this->cols;

        $method->setCodeLine(function ($lines) use ($cols)
        {
            $lines[] = '$load = array();';
            $lines[] = '';
            $lines[] = '// List editable fields from table ---';

            foreach ($cols as $index => $col)
            {
                if ($col->isPrimaryKey())
                {
                    continue;
                }

                $name = $col->getField();

                $lines[] = '/** @var ' . $col->getDataType() . ' */';
                $lines[] = '$' . $name . ' = $data[\'' . $name . '\'];';
                $lines[] = '$load[\'' . $name . '\'] = $' . $name . ';';

                if (array_key_exists($index + 1, $cols))
                {
                    $lines[] = '';
                }
            }

            $lines[] = '// -----------------------------------';
            $lines[] = '';
            $lines[] = 'return $load;';

            return $lines;
        });

        $this->addMethod($method);
    }

    /**
     * @param string $link
     *
     * @return void
     */
    protected function setPagee($link)
    {
        $default = array();
        $default['page_query_string'] = true;
        $default['use_page_numbers'] = true;
        $default['query_string_segment'] = 'p';
        $default['reuse_query_string'] = true;

        $this->addArrayProperty('pagee', 'array<string, mixed>')
            ->withComment('Additional configuration to Pagination Class.')
            ->withLink($link . '/pagination.html#customizing-the-pagination')
            ->withDefaultValue($default);
    }

    /**
     * @return void
     */
    protected function setProperties()
    {
        foreach ($this->cols as $col)
        {
            $type = $col->getDataType();

            $name = $col->getField();

            $isNull = $col->isNull();

            switch ($type)
            {
                case 'string':
                    $this->addStringProperty($name, $isNull)->asTag();

                    break;
                case 'integer':
                    $this->addIntegerProperty($name, $isNull)->asTag();

                    break;
            }
        }

        $this->addClassProperty('db', 'CI_DB_query_builder')->asTag();
    }

    /**
     * @param string $link
     *
     * @return void
     */
    protected function setRules($link)
    {
        $rules = array();

        foreach ($this->cols as $col)
        {
            if ($col->isNull() || $col->isPrimaryKey())
            {
                continue;
            }

            $name = $col->getField();

            $rule = array('field' => $name);

            $rule['label'] = ucfirst($name);

            $rule['rules'] = 'required';

            $rules[] = $rule;
        }

        $this->addArrayProperty('rules', 'array<string, string>[]')
            ->withComment('List of validation rules for Form Validation.')
            ->withLink($link . '/form_validation.html#setting-rules-using-an-array')
            ->withDefaultValue($rules);
    }
}
