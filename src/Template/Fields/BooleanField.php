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
     * @var string
     */
    protected $class = 'form-check-input';

    /**
     * @var string
     */
    protected $type = 'boolean';

    /**
     * @return string[]
     */
    public function getPlate()
    {
        $field = $this->accessor;

        $tab = $this->tab;

        $class = $this->getClass();

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
}
