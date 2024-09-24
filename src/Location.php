<?php

namespace Rougin\Combustor;

/**
 * @package Combustor
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class Location
{
    /**
     * @var string
     */
    protected $root;

    /**
     * @param string $root
     */
    public function __construct($root)
    {
        $this->root = $root;
    }

    /**
     * @return string
     */
    public function getAppPath()
    {
        $root = $this->root;

        // @codeCoverageIgnoreStart
        if (is_dir($this->root . '/application'))
        {
            $root = $this->root . '/application';
        }
        // @codeCoverageIgnoreEnd

        return (string) $root;
    }
}
