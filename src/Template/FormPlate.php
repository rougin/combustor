<?php

namespace Rougin\Combustor\Template;

use Rougin\Combustor\Inflector;
use Rougin\Combustor\Template\Fields\BooleanField;
use Rougin\Combustor\Template\Fields\DefaultField;
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

            $title = Inflector::humanize($name);

            $class = $this->bootstrap ? 'mb-3' : '';
            $lines[] = $tab . '<div class="' . $class . '">';
            $class = $this->bootstrap ? 'form-label mb-0' : '';
            $lines[] = $tab . $tab . '<?= form_label(\'' . $title . '\', \'\', [\'class\' => \'' . $class . '\']) ?>';

            $field = $this->getField($col, $tab);

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

        // Replace all empty class placeholders -------------
        $result = str_replace(' class=""', '', $result);

        $result = str_replace(', \'class=""\'', '', $result);

        $search = ', \'\', [\'class\' => \'\']';

        return str_replace($search, '', $result);
        // --------------------------------------------------
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
     * @param string[] $excluded
     *
     * @return self
     */
    public function withExcluded($excluded)
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
            $name = 'get_' . $name . '()';
        }

        return '$item->' . $name;
    }

    /**
     * @param \Rougin\Describe\Column $column
     * @param string                  $tab
     *
     * @return \Rougin\Combustor\Colfield
     */
    protected function getField(Column $column, $tab = '')
    {
        $name = $this->getAccessor($column);

        $field = new DefaultField($this->edit, $tab);
        $field->withName($column->getField());

        $class = $this->bootstrap ? 'form-control' : '';
        $field->withClass($class);

        $type = $column->getDataType();

        if ($type === 'boolean')
        {
            $field = new BooleanField($this->edit, $tab);
            $field->withName($column->getField());

            $class = $this->bootstrap ? 'form-check-input' : '';
            $field->withClass($class);
        }

        return $field->withAccessor($name);
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
