<?php

namespace Rougin\Combustor\Template;

/**
 * @package Combustor
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class HeaderPlate
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
        $lines = array('<!DOCTYPE html>');

        $lines[] = '<html lang="en">';
        $lines[] = '<head>';

        $lines[] = $tab . '<meta charset="utf-8">';
        $lines[] = $tab . '<meta http-equiv="X-UA-Compatible" content="IE=edge">';
        $lines[] = $tab . '<meta name="viewport" content="width=device-width, initial-scale=1">';
        $lines[] = $tab . '<title>Welcome to Codeigniter 3</title>';

        if ($this->bootstrap)
        {
            $lines[] = $tab . '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">';
        }

        $lines[] = '</head>';
        $lines[] = '<body>';

        if ($this->bootstrap)
        {
            $lines[] = $tab . '<div class="container">';
        }
        else
        {
            $lines[] = $tab . '<div>';
        }

        return implode("\n", $lines);
    }
}
