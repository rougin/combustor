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
     * @var string[]
     */
    protected $excluded = array();

    /**
     * @param string                    $table
     * @param \Rougin\Describe\Column[] $cols
     * @param string[]                  $excluded
     */
    public function __construct($table, $cols, $excluded = array())
    {
        $this->cols = $cols;

        $this->excluded = $excluded;

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

        $this->addStringProperty('table')
            ->withComment('The table associated with the model.')
            ->withDefaultValue($table);

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
                $name = $col->getField();

                if ($col->isPrimaryKey() || in_array($name, $this->excluded))
                {
                    continue;
                }

                $type = $col->getDataType();

                if ($col->isNull())
                {
                    $type = $type . '|null';
                }

                if ($col->isNull() || $type === 'boolean')
                {
                    $lines[] = 'if (array_key_exists(\'' . $name . '\', $data))';
                    $lines[] = '{';
                    $lines[] = '    /** @var ' . $type . ' */';
                    $lines[] = '    $' . $name . ' = $data[\'' . $name . '\'];';
                    $lines[] = '    $load[\'' . $name . '\'] = $' . $name . ';';
                    $lines[] = '}';
                }
                else
                {
                    $lines[] = '/** @var ' . $type . ' */';
                    $lines[] = '$' . $name . ' = $data[\'' . $name . '\'];';
                    $lines[] = '$load[\'' . $name . '\'] = $' . $name . ';';
                }

                if (array_key_exists($index + 1, $cols))
                {
                    $next = $cols[$index + 1];

                    if (! in_array($next->getField(), $this->excluded))
                    {
                        $lines[] = '';
                    }
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
                case 'boolean':
                    $this->addBooleanProperty($name, $isNull)->asTag();

                    break;
                case 'integer':
                    $this->addIntegerProperty($name, $isNull)->asTag();

                    break;
                default:
                    $this->addStringProperty($name, $isNull)->asTag();

                    break;
            }
        }
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
            $name = $col->getField();

            $isExcluded = in_array($name, $this->excluded);

            if ($col->isNull() || $col->isPrimaryKey() || $isExcluded)
            {
                continue;
            }

            // Do not include boolean types in validation ---
            if ($col->getDataType() === 'boolean')
            {
                continue;
            }
            // ----------------------------------------------

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
