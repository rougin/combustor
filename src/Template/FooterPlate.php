<?php

namespace Rougin\Combustor\Template;

/**
 * @package Combustor
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class FooterPlate
{
    /**
     * @var boolean
     */
    protected $bootstrap;

    /**
     * @param boolean $bootstrap
     */
    public function __construct($bootstrap)
    {
        $this->bootstrap = $bootstrap;
    }

    /**
     * @param string $tab
     *
     * @return string
     */
    public function make($tab = '  ')
    {
        $lines = array();

        if ($this->bootstrap)
        {
            $lines[] = $tab . '<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>';
            $lines[] = $tab . '<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>';
        }

        $lines[] = '</body>';
        $lines[] = '</html>';

        return implode("\n", $lines);
    }
}
