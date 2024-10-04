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
     * @param string $tab
     *
     * @return string
     */
    public function make($tab = '  ')
    {
        $lines = array('</body>');

        $lines[] = '</html>';

        return implode("\n", $lines);
    }
}
