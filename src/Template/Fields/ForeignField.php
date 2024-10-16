<?php

namespace Rougin\Combustor\Template\Fields;

use Rougin\Combustor\Colfield;
use Rougin\Combustor\Inflector;

/**
 * @package Combustor
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class ForeignField extends Colfield
{
    /**
     * @var string
     */
    protected $class = 'form-control';

    /**
     * @var string
     */
    protected $table;

    /**
     * @return string[]
     */
    public function getPlate()
    {
        $field = $this->accessor;

        $class = $this->getClass();

        $name = $this->getName();

        $items = Inflector::plural($this->table);

        $html = '<?= form_dropdown(\'' . $name . '\', $' . $items . ', set_value(\'' . $name . '\'), \'class="' . $class . '"\') ?>';

        if ($this->edit)
        {
            $html = str_replace('set_value(\'' . $name . '\')', 'set_value(\'' . $name . '\', ' . $field . ')', $html);
        }

        return array($html);
    }

    /**
     * @param string $table
     *
     * @return self
     */
    public function setTableName($table)
    {
        $this->table = $table;

        return $this;
    }
}
