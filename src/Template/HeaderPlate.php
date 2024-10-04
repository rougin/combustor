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

        $lines[] = '</head>';
        $lines[] = '<body>';

        $lines[] = $tab . '<div>';

        return implode("\n", $lines);
    }
}
