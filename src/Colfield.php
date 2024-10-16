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
     * @var boolean
     */
    protected $styling = false;

    /**
     * @var string
     */
    protected $tab = '';

    /**
     * @var string|null
     */
    protected $type = null;

    /**
     * @param boolean $edit
     *
     * @return self
     */
    public function asEdit($edit = true)
    {
        $this->edit = $edit;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getClass()
    {
        if ($this->styling)
        {
            return $this->class;
        }

        return null;
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
     * @return string|null
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $accessor
     *
     * @return self
     */
    public function setAccessor($accessor)
    {
        $this->accessor = $accessor;

        return $this;
    }

    /**
     * @param string $name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param string $tab
     *
     * @return self
     */
    public function setSpacing($tab = '')
    {
        $this->tab = $tab;

        return $this;
    }

    /**
     * @param boolean $styling
     *
     * @return self
     */
    public function useStyling($styling = true)
    {
        $this->styling = $styling;

        return $this;
    }
}
