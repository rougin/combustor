<?php

namespace Rougin\Combustor\Template;

use Rougin\Combustor\Inflector;
use Rougin\Combustor\Template\Fields\DefaultField;
use Rougin\Combustor\Template\Fields\ForeignField;
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
     * @var boolean
     */
    protected $bootstrap = false;

    /**
     * @var \Rougin\Describe\Column[]
     */
    protected $cols;

    /**
     * @var \Rougin\Combustor\Colfield[]
     */
    protected $customs = array();

    /**
     * @var boolean
     */
    protected $edit = false;

    /**
     * @var string[]
     */
    protected $excluded = array();

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
        $this->cols = $cols;

        $this->type = $type;

        $this->table = $table;
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

        $title = $this->edit ? 'Update' : 'Create New';
        $title = $title . ' ' . ucfirst($model);

        $lines = array('<h1>' . $title . '</h1>');
        $lines[] = '';

        $link = $route . '/create\'';

        if ($this->edit)
        {
            $primary = $this->getPrimary();

            $id = $primary ? '/\' . ' . $this->getAccessor($primary) : '';

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
            $name = $col->getField();

            if ($col->isPrimaryKey() || in_array($name, $this->excluded))
            {
                continue;
            }

            $title = $this->getFieldTitle($col);

            $class = $this->bootstrap ? 'mb-3' : '';
            $lines[] = $tab . '<div class="' . $class . '">';
            $class = $this->bootstrap ? 'form-label mb-0' : '';
            $lines[] = $tab . $tab . '<?= form_label(\'' . $title . '\', \'\', [\'class\' => \'' . $class . '\']) ?>';

            $field = $this->getFieldPlate($col, $tab);

            foreach ($field->getPlate() as $plate)
            {
                $lines[] = $tab . $tab . $plate;
            }

            $class = $this->bootstrap ? 'text-danger small' : '';
            $lines[] = $tab . $tab . '<?= form_error(\'' . $name . '\', \'<div><span class="' . $class . '">\', \'</span></div>\') ?>';
            $lines[] = $tab . '</div>';
            $lines[] = '';
        }

        $submit = $this->edit ? 'Update' : 'Create';

        $lines[] = $tab . '<?php if (isset($error)): ?>';
        $class = $this->bootstrap ? 'alert alert-danger' : '';
        $lines[] = $tab . '  <div class="' . $class . '"><?= $error ?></div>';
        $lines[] = $tab . '<?php endif ?>';
        $lines[] = '';

        $class = $this->bootstrap ? 'btn btn-link text-secondary text-decoration-none' : '';
        $lines[] = $tab . '<?= anchor(\'' . $route . '\', \'Cancel\', \'class="' . $class . '"\') ?>';
        $class = $this->bootstrap ? 'btn btn-primary' : '';
        $lines[] = $tab . '<?= form_submit(null, \'' . $submit . '\', \'class="' . $class . '"\') ?>';
        $lines[] = '<?= form_close() ?>';

        $result = implode("\n", $lines);

        // Replace all empty class placeholders ------------------
        $result = str_replace(' class=""', '', $result);

        $result = str_replace(', \'class\' => \'\'', '', $result);

        $result = str_replace(', \'class=""\'', '', $result);

        $search = ', \'\', [\'class\' => \'\']';

        return str_replace($search, '', $result);
        // -------------------------------------------------------
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

    /**
     * @param \Rougin\Combustor\Colfield[] $customs
     *
     * @return self
     */
    public function withCustomFields($customs)
    {
        $this->customs = $customs;

        return $this;
    }

    /**
     * @param string[] $excluded
     *
     * @return self
     */
    public function withExcludedFields($excluded)
    {
        $this->excluded = $excluded;

        return $this;
    }

    /**
     * @param \Rougin\Describe\Column $column
     *
     * @return string
     */
    protected function getAccessor(Column $column)
    {
        $name = $column->getField();

        if ($this->type === self::TYPE_DOCTRINE)
        {
            // TODO: Use a single function for this code -------
            $method = 'get_' . $name;

            if ($column->getDataType() === 'boolean')
            {
                // Remove "is_" from name to get proper name ---
                $temp = str_replace('is_', '', $name);
                // ---------------------------------------------

                $method = 'is_' . $temp;
            }
            // -------------------------------------------------

            $name = $method . '()';
        }

        return '$item->' . $name;
    }

    /**
     * @param \Rougin\Describe\Column $column
     * @param string                  $tab
     *
     * @return \Rougin\Combustor\Colfield
     */
    protected function getFieldPlate(Column $column, $tab = '')
    {
        $name = $this->getAccessor($column);

        $field = new DefaultField;

        foreach ($this->customs as $custom)
        {
            $isField = $custom->getName() === $column->getField();

            $isType = $custom->getType() === $column->getDataType();

            if ($isField || $isType)
            {
                $field = $custom;
            }
        }

        if ($column->isForeignKey())
        {
            $field = new ForeignField;

            $field->setTableName($column->getReferencedTable());
        }

        $field->setName($column->getField());

        if ($this->bootstrap)
        {
            $field->useStyling(true);
        }

        $field->asEdit($this->edit);

        $field->setSpacing($tab);

        return $field->setAccessor($name);
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
