<?php

namespace Rougin\Combustor\Template\Fields;

use Rougin\Combustor\Colfield;

/**
 * @package Combustor
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class BooleanField extends Colfield
{
    /**
     * @return string[]
     */
    public function getPlate()
    {
        $class = $this->class;

        $tab = $this->tab;

        $field = $this->accessor;

        $name = $this->getName();

        $lines = array('<div>');

        if ($this->edit)
        {
            $lines[] = $tab . '<?= form_checkbox(\'' . $name . '\', true, set_value(\'' . $name . '\', ' . $field . '), \'class="' . $class . '"\') ?>';
        }
        else
        {
            $lines[] = $tab . '<?= form_checkbox(\'' . $name . '\', true, set_value(\'' . $name . '\'), \'class="' . $class . '"\') ?>';
        }

        $lines[] = '</div>';

        return $lines;
    }

    /**
     * @param string $name
     *
     * @return self
     */
    public function withName($name)
    {
        $this->name = $name;

        return $this;
    }
}
