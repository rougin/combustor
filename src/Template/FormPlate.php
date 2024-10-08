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
            $name = $col->getField();

            if ($col->isPrimaryKey() || in_array($name, $this->excluded))
            {
                continue;
            }

            $title = Inflector::humanize($name);

            $field = $this->getField($col);

            $class = $this->bootstrap ? 'mb-3' : '';
            $lines[] = $tab . '<div class="' . $class . '">';
            $class = $this->bootstrap ? 'form-label mb-0' : '';
            $lines[] = $tab . $tab . '<?= form_label(\'' . $title . '\', \'\', [\'class\' => \'' . $class . '\']) ?>';

            $class = $this->bootstrap ? 'form-control' : '';

            if ($this->edit)
            {
                $lines[] = $tab . $tab . '<?= form_input(\'' . $name . '\', set_value(\'' . $name . '\', ' . $field . '), \'class="' . $class . '"\') ?>';
            }
            else
            {
                $lines[] = $tab . $tab . '<?= form_input(\'' . $name . '\', set_value(\'' . $name . '\'), \'class="' . $class . '"\') ?>';
            }

            $class = $this->bootstrap ? 'text-danger small' : '';
            $lines[] = $tab . $tab . '<?= form_error(\'' . $name . '\', \'<div><span class="' . $class . '">\', \'</span></div>\') ?>';
            $lines[] = $tab . '</div>';
            $lines[] = '';
        }

        $submit = 'Create';

        if ($this->edit)
        {
            $submit = 'Update';
        }

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
