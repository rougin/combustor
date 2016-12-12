<?php

namespace Rougin\Combustor\Common;

/**
 * Twig extension for CodeIgniter's inflector helper.
 *
 * @package Combustor
 * @author  Rougin Royce Gutib <rougingutib@gmail.com>
 */
class InflectorExtension extends \Twig_Extension
{
    /**
     * Get twig filters
     *
     * @return array filters
     */
    public function getFilters()
    {
        return [
            'plural'     => new \Twig_SimpleFilter('plural', [ $this, 'toPluralFormat' ]),
            'singular'   => new \Twig_SimpleFilter('singular', [ $this, 'toSingularFormat' ]),
            'title'      => new \Twig_SimpleFilter('title', [ $this, 'toTitleCase' ]),
            'underscore' => new \Twig_SimpleFilter('underscore', [ $this, 'toUnderscoreCase' ]),
        ];
    }

    /**
     * Takes a singular word and makes it plural.
     *
     * @param  string $input
     * @return string
     */
    public function toPluralFormat($input)
    {
        return plural($input);
    }

    /**
     * Takes a plural word and makes it singular.
     *
     * @param  string $input
     * @return string
     */
    public function toSingularFormat($input)
    {
        return singular($input);
    }

    /**
     * Convert string to underscore case format
     *
     * @param  string $input
     * @return string In underscore case
     */
    public function toUnderscoreCase($input)
    {
        return underscore($input);
    }

    /**
     * Convert string to title case format.
     *
     * @param  string $input
     * @return string In title case
     */
    public function toTitleCase($input)
    {
        return ucwords(humanize($input));
    }

    /**
     * Get twig extension name
     *
     * @return string
     */
    public function getName()
    {
        return 'InflectorExtension';
    }
}
