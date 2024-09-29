<?php

namespace Rougin\Combustor\Template;

/**
 * @package Combustor
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class TablePlate
{
    /**
     * @var \Rougin\Describe\Column[]
     */
    protected $cols;

    /**
     * @param \Rougin\Describe\Column[] $cols
     */
    public function __construct($cols)
    {
        $this->cols = $cols;
    }

    /**
     * @param string $tab
     *
     * @return string[]
     */
    public function make($tab = '')
    {
        $lines = array();
        $lines[] = $tab . '<table>';

        $lines[] = $tab . '</table>';
        return $lines;
    }
}
