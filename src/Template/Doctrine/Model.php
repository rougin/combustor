<?php

namespace Rougin\Combustor\Template\Doctrine;

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

        $repo = ucfirst($name) . '_repository';

        $comment = array('@Entity(repositoryClass="' . $repo . '")');
        $comment[] = '';
        $comment[] = '@Table(name="' . $table . '")';
        $this->setComment($comment);

        $link = 'https://codeigniter.com/userguide3/libraries';

        // Sort columns by name ------------
        $items = array();

        foreach ($this->cols as $col)
        {
            $items[$col->getField()] = $col;
        }

        ksort($items);
        // ---------------------------------

        $this->setProperties($items);

        $this->setPagee($link);

        $this->setRules($link);

        $this->setMethods($items);
    }

    /**
     * @param \Rougin\Describe\Column[] $cols
     *
     * @return void
     */
    protected function setMethods($cols)
    {
        foreach ($cols as $col)
        {
            // Add additional method for foreign keys --------
            if ($col->isForeignKey())
            {
                $name = $col->getReferencedTable();
                $name = Inflector::singular($name);

                $method = 'get_' . $name;

                $method = new Method($method);

                $type = '\\' . ucfirst($name);

                if ($col->isNull())
                {
                    $type = $type . '|null';
                }

                $method->setReturn($type);

                $fn = function ($lines) use ($name)
                {
                    $lines[] = 'return $this->' . $name . ';';

                    return $lines;
                };

                $method->setCodeLine($fn);

                $this->addMethod($method);
            }
            // -----------------------------------------------

            $name = $col->getField();

            $name = Inflector::snakeCase($name);

            $type = $col->getDataType();

            if ($col->isNull())
            {
                $type = $type . '|null';
            }

            // TODO: Use a single function for this code -------
            $method = 'get_' . $name;

            if ($col->getDataType() === 'boolean')
            {
                // Remove "is_" from name to get proper name ---
                $temp = str_replace('is_', '', $name);
                // ---------------------------------------------

                $method = 'is_' . $temp;
            }
            // -------------------------------------------------

            $method = new Method($method);

            $method->setReturn($type);

            $fn = function ($lines) use ($name)
            {
                $lines[] = 'return $this->' . $name . ';';

                return $lines;
            };

            $method->setCodeLine($fn);

            $this->addMethod($method);
        }

        foreach ($cols as $col)
        {
            if ($col->isPrimaryKey())
            {
                continue;
            }

            $name = $col->getField();

            $name = Inflector::snakeCase($name);

            $type = $col->getDataType();

            if ($col->isForeignKey())
            {
                $name = $col->getReferencedTable();
                $name = Inflector::singular($name);

                $type = ucfirst($name);
            }

            $method = new Method('set_' . $name);

            $method->setReturn('self');

            $isNull = $col->isNull();

            switch ($type)
            {
                case 'boolean':
                    $method->addBooleanArgument($name, $isNull);

                    break;
                case 'integer':
                    $method->addIntegerArgument($name, $isNull);

                    break;
                default:
                    if ($col->isForeignKey())
                    {
                        $method->addClassArgument($name, ucfirst($name), $isNull);
                    }
                    else
                    {
                        $method->addStringArgument($name, $isNull);
                    }

                    break;
            }

            $fn = function ($lines) use ($name)
            {
                $lines[] = '$this->' . $name . ' = $' . $name . ';';
                $lines[] = '';
                $lines[] = 'return $this;';

                return $lines;
            };

            $method->setCodeLine($fn);

            $this->addMethod($method);
        }
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
     * @param \Rougin\Describe\Column[] $cols
     *
     * @return void
     */
    protected function setProperties($cols)
    {
        foreach ($cols as $col)
        {
            $isNull = $col->isNull();

            $isUnique = $col->isUnique();

            $name = $col->getField();

            $name = Inflector::snakeCase($name);

            if ($col->isForeignKey())
            {
                $foreignTable = $col->getReferencedTable();
                $foreignName = Inflector::singular($foreignTable);
                $class = ucfirst($foreignName);
                $foreign = $col->getReferencedField();

                $this->addClassProperty($foreignName, $class, $isNull);

                // Generate Doctrine annotations to foreign key -------
                $lines = array();

                $keys = array('targetEntity="' . $class . '"');
                $keys[] = 'cascade={"persist"}';
                $lines[] = '@ManyToOne(' . implode(', ', $keys) . ')';

                $keys = array('name="' . $name . '"');
                $keys[] = 'referencedColumnName="' . $foreign . '"';
                $keys[] = 'nullable=' . ($isNull ? 'true' : 'false');
                $keys[] = 'unique=' . ($isUnique ? 'true' : 'false');
                $lines[] = '@JoinColumn(' . implode(', ', $keys) . ')';

                $this->withComment($lines);
                // ----------------------------------------------------
            }

            $type = $col->getDataType();

            switch ($type)
            {
                case 'boolean':
                    $this->addBooleanProperty($name, $isNull);

                    break;
                case 'integer':
                    $this->addIntegerProperty($name, $isNull);

                    break;
                default:
                    $this->addStringProperty($name, $isNull);

                    break;
            }

            // Generate Doctrine annotations to columns ---------
            $lines = array();

            if ($col->isPrimaryKey())
            {
                $lines[] = '@Id @GeneratedValue';
                $lines[] = '';
            }

            $keys = array('name="' . $name . '"');
            $keys[] = 'type="' . $type . '"';

            if ($length = $col->getLength())
            {
                $keys[] = 'length=' . $length;
            }

            $keys[] = 'nullable=' . ($isNull ? 'true' : 'false');
            $keys[] = 'unique=' . ($isUnique ? 'true' : 'false');

            $lines[] = '@Column(' . implode(', ', $keys) . ')';
            // --------------------------------------------------

            $this->withComment($lines);
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
