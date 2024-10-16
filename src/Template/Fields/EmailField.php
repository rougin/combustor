<?php

namespace Rougin\Combustor\Template\Fields;

use Rougin\Combustor\Colfield;

/**
 * @package Combustor
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class EmailField extends Colfield
{
    /**
     * @var string
     */
    protected $class = 'form-control';

    /**
     * @var string
     */
    protected $name = 'email';

    /**
     * @return string[]
     */
    public function getPlate()
    {
        $field = $this->accessor;

        $class = $this->getClass();

        /** @var string */
        $name = $this->getName();

        $html = '<?= form_input([\'type\' => \'email\', \'name\' => \'' . $name . '\', \'value\' => set_value(\'' . $name . '\')]) ?>';

        if ($this->edit)
        {
            $html = str_replace('set_value(\'' . $name . '\')', 'set_value(\'' . $name . '\', ' . $field . ')', $html);
        }

        $html = str_replace(')]) ?>', '), \'class\' => \'' . $class . '\']) ?>', $html);

        return array($html);
    }
}
