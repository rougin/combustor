<?php

namespace Rougin\Combustor;

/**
 * @package Combustor
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class Colfield
{
    /**
     * @var string|null
     */
    protected $accessor = null;

    /**
     * @var string|null
     */
    protected $class = null;

    /**
     * @var boolean
     */
    protected $edit = false;

    /**
     * @var string|null
     */
    protected $name = null;

    /**
     * @var string
     */
    protected $tab = '';

    /**
     * @param boolean $edit
     * @param string  $tab
     */
    public function __construct($edit = false, $tab = '')
    {
        $this->edit = $edit;

        $this->tab = $tab;
    }

    /**
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @codeCoverageIgnore
     *
     * @return string[]
     */
    public function getPlate()
    {
        return array();
    }

    /**
     * @param string $accessor
     *
     * @return self
     */
    public function withAccessor($accessor)
    {
        $this->accessor = $accessor;

        return $this;
    }

    /**
     * @param string $class
     *
     * @return self
     */
    public function withClass($class)
    {
        $this->class = $class;

        return $this;
    }
}
