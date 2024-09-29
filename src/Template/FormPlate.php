<?php

namespace Rougin\Combustor\Template;

use Rougin\Combustor\Inflector;
use Rougin\Describe\Column;

/**
 * @package Combustor
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class FormPlate
{
    const TYPE_WILDFIRE = 0;

    const TYPE_DOCTRINE = 1;

    /**
     * @var \Rougin\Describe\Column[]
     */
    protected $cols;

    /**
     * @var boolean
     */
    protected $edit = false;

    /**
     * @var string
     */
    protected $table;

    /**
     * @var integer
     */
    protected $type;

    /**
     * @param string                    $table
     * @param integer                   $type
     * @param \Rougin\Describe\Column[] $cols
     */
    public function __construct($table, $type, $cols)
    {
        $this->type = $type;

        $this->table = $table;

        $this->cols = $cols;
    }

    /**
     * @param string $tab
     *
     * @return string
     */
    public function make($tab = '')
    {
        $model = Inflector::singular($this->table);

        $route = Inflector::plural($this->table);

        $title = 'Create New ' . ucfirst($model);

        if ($this->edit)
        {
            $title = 'Update ' . ucfirst($model);
        }

        $lines = array('<h1>' . $title . '</h1>');
        $lines[] = '';

        $link = $route . '/create\'';

        if ($this->edit)
        {
            $primary = $this->getPrimary();

            $id = null;

            if ($primary)
            {
                $id = '/\' . ' . $this->getField($primary);
            }

            $link = $route . '/edit' . $id;
        }

        $lines[] = '<?= form_open(\'' . $link . ') ?>';

        if ($this->edit)
        {
            $lines[] = $tab . '<?= form_hidden(\'_method\', \'PUT\') ?>';
            $lines[] = '';
        }

        foreach ($this->cols as $col)
        {
            if ($col->isPrimaryKey())
            {
                continue;
            }

            $name = $col->getField();

            $title = Inflector::humanize($name);

            $field = $this->getField($col);

            $lines[] = $tab . '<div>';
            $lines[] = $tab . $tab . '<?= form_label(\'' . $title . '\') ?>';

            if ($this->edit)
            {
                $lines[] = $tab . $tab . '<?= form_input(\'' . $name . '\', set_value(\'' . $name . '\', ' . $field . ')) ?>';
            }
            else
            {
                $lines[] = $tab . $tab . '<?= form_input(\'' . $name . '\', set_value(\'' . $name . '\')) ?>';
            }

            $lines[] = $tab . $tab . '<?= form_error(\'' . $name . '\', \'<div><span>\', \'</span></div>\') ?>';
            $lines[] = $tab . '</div>';
            $lines[] = '';
        }

        $submit = 'Create';

        if ($this->edit)
        {
            $submit = 'Update';
        }

        $lines[] = $tab . '<div><?= isset($error) ? $error : \'\' ?></div>';
        $lines[] = '';
        $lines[] = $tab . '<?= anchor(\'' . $route . '\', \'Cancel\') ?>';
        $lines[] = $tab . '<?= form_submit(null, \'' . $submit . '\') ?>';
        $lines[] = '<?= form_close() ?>';

        return implode("\n", $lines);
    }

    /**
     * @param \Rougin\Describe\Column $column
     *
     * @return string
     */
    protected function getField(Column $column)
    {
        $name = $column->getField();

        if ($this->type === self::TYPE_DOCTRINE)
        {
            $name = 'get_' . $name . '()';
        }

        return '$item->' . $name;
    }

    /**
     * @return \Rougin\Describe\Column|null
     */
    protected function getPrimary()
    {
        $primary = null;

        foreach ($this->cols as $col)
        {
            if ($col->isPrimaryKey())
            {
                $primary = $col;
            }
        }

        return $primary;
    }
}
