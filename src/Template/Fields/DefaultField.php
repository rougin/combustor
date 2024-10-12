<?php

namespace Rougin\Combustor\Template\Fields;

use Rougin\Combustor\Colfield;

/**
 * @package Combustor
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class DefaultField extends Colfield
{
    /**
     * @var string
     */
    protected $class = 'form-control';

    /**
     * @return string[]
     */
    public function getPlate()
    {
        $field = $this->accessor;

        $class = $this->getClass();

        $name = $this->getName();

        $html = '<?= form_input(\'' . $name . '\', set_value(\'' . $name . '\'), \'class="' . $class . '"\') ?>';

        if ($this->edit)
        {
            $html = '<?= form_input(\'' . $name . '\', set_value(\'' . $name . '\', ' . $field . '), \'class="' . $class . '"\') ?>';
        }

        return array($html);
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
