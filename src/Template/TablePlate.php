<?php

namespace Rougin\Combustor\Template;

use Rougin\Combustor\Inflector;
use Rougin\Describe\Column;

/**
 * @package Combustor
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class TablePlate
{
    const TYPE_WILDFIRE = 0;

    const TYPE_DOCTRINE = 1;

    /**
     * @var boolean
     */
    protected $bootstrap = false;

    /**
     * @var \Rougin\Describe\Column[]
     */
    protected $cols;

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
     * @param \Rougin\Describe\Column[] $cols
     * @param integer                   $type
     */
    public function __construct($table, $cols, $type)
    {
        $this->cols = $cols;

        $this->table = $table;

        $this->type = $type;
    }

    /**
     * @param string $tab
     *
     * @return string[]
     */
    public function make($tab = '')
    {
        $class = $this->bootstrap ? 'table table-hover' : '';

        $lines = array('<table class="' . $class . '">');

        $lines = $this->setCol($lines, $tab);

        $lines = $this->setRow($lines, $tab);

        $lines[] = '</table>';

        return $lines;
    }

    /**
     * @param \Rougin\Describe\Column $column
     *
     * @return string
     */
    protected function getFieldTitle(Column $column)
    {
        $name = $column->getField();

        if ($column->isForeignKey())
        {
            $name = $column->getReferencedTable();

            $name = Inflector::singular($name);
        }

        return Inflector::humanize($name);
    }

    /**
     * @param string[] $lines
     * @param string   $tab
     *
     * @return string[]
     */
    protected function setCol($lines, $tab = '')
    {
        $lines[] = $tab . '<thead>';
        $lines[] = $tab . $tab . '<tr>';

        $space = $tab . $tab . $tab;

        foreach ($this->cols as $col)
        {
            if ($col->isPrimaryKey())
            {
                continue;
            }

            $name = $this->getFieldTitle($col);

            $lines[] = $space . '<th>' . $name . '</th>';
        }

        $lines[] = $space . '<th></th>';

        $lines[] = $tab . $tab . '</tr>';
        $lines[] = $tab . '</thead>';

        return $lines;
    }

    /**
     * @param string[] $lines
     * @param string   $tab
     *
     * @return string[]
     */
    protected function setRow($lines, $tab = '')
    {
        $lines[] = $tab . '<tbody>';
        $lines[] = $tab . $tab . '<?php foreach ($items as $item): ?>';
        $lines[] = $tab . $tab . $tab . '<tr>';

        $space = $tab . $tab . $tab . $tab;

        $primary = null;

        foreach ($this->cols as $col)
        {
            if ($col->isPrimaryKey())
            {
                $primary = $col;

                continue;
            }

            $name = '<?= $item->[FIELD] ?>';

            $field = $col->getField();

            if ($this->type === self::TYPE_DOCTRINE)
            {
                $field = 'get_' . $col->getField();

                $field = Inflector::snakeCase($field);

                $field = $field . '()';
            }

            $name = str_replace('[FIELD]', $field, $name);

            $lines[] = $space . '<td>' . $name . '</td>';
        }

        if ($primary)
        {
            // Set the primary key field -----------
            $id = $primary->getField();

            if ($this->type === self::TYPE_DOCTRINE)
            {
                $id = 'get_' . $id . '()';
            }

            $field = '$item->' . $id;
            // -------------------------------------

            $route = Inflector::plural($this->table);

            $class = $this->bootstrap ? 'd-flex' : '';
            $lines[] = $space . '<td>';
            $lines[] = $space . $tab . '<div class="' . $class . '">';

            $lines[] = $space . $tab . $tab . '<span>';
            $class = $this->bootstrap ? 'btn btn-secondary btn-sm' : '';
            $link = '<?= base_url(\'' . $route . '/edit/\' . ' . $field . ') ?>';
            $link = '<a class="' . $class . '" href="' . $link . '">Edit</a>';
            $lines[] = $space . $tab . $tab . $tab . $link;
            $lines[] = $space . $tab . $tab . '</span>';

            $lines[] = $space . $tab . $tab . '<span>';

            $form = '<?= form_open(\'' . $route . '/delete/\' . ' . $field . ') ?>';
            $lines[] = $space . $tab . $tab . $tab . $form;

            $hidden = '<?= form_hidden(\'_method\', \'DELETE\') ?>';
            $lines[] = $space . $tab . $tab . $tab . $tab . $hidden;

            $class = $this->bootstrap ? 'btn btn-link btn-sm text-danger text-decoration-none' : '';
            $delete = '<a class="' . $class . '" href="javascript:void(0)" onclick="trash(this.parentElement)">Delete</a>';
            $lines[] = $space . $tab . $tab . $tab . $tab . $delete;

            $close = '<?= form_close() ?>';
            $lines[] = $space . $tab . $tab . $tab . $close;

            $lines[] = $space . $tab . $tab . '</span>';

            $lines[] = $space . $tab . '</div>';
            $lines[] = $space . '</td>';
        }

        $lines[] = $tab . $tab . $tab . '</tr>';
        $lines[] = $tab . $tab . '<?php endforeach ?>';
        $lines[] = $tab . '</tbody>';

        return $lines;
    }

    /**
     * @param boolean $bootstrap
     *
     * @return self
     */
    public function withBootstrap($bootstrap)
    {
        $this->bootstrap = $bootstrap;

        return $this;
    }
}
