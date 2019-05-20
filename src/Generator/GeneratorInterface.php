<?php

namespace Rougin\Combustor\Generator;

/**
 * Generator Interface
 *
 * An interface for generators.
 *
 * @package Combustor
 * @author  Rougin Gutib <rougingutib@gmail.com>
 */
interface GeneratorInterface
{
    /**
     * Generates set of code based on data.
     *
     * @return array
     */
    public function generate();
}
