<?php

namespace Rougin\Combustor;

use Rougin\Describe\Describe;

/**
 * Validator
 *
 * Checks if Wildfire or Doctrine is available
 * 
 * @package Combustor
 * @author  Rougin Royce Gutib <rougingutib@gmail.com>
 */
class Validator
{
    protected $describe;
    protected $doesExists;
    protected $message;
    protected $type;

    /**
     * @param Describe $describe
     * @param string   $doesExists
     */
    public function __construct(Describe $describe, $doesExists)
    {
        $this->describe = $describe;
        $this->doesExists = $doesExists;
    }

    /**
     * Get the error message
     * 
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Get the existing type
     * 
     * @return string
     */
    public function getType()
    {
        $hasDoctrine = file_exists(APPPATH . '/libraries/Doctrine.php');
        $hasWildfire = file_exists(APPPATH . '/libraries/Wildfire.php');

        if (!$this->doesExists['doctrine'] && !$this->doesExists['wildfire']) {
            if (file_exists(APPPATH . 'libraries/Wildfire.php')) {
                $hasWildfire = TRUE;
            }

            if (file_exists(APPPATH . 'libraries/Doctrine.php')) {
                $hasDoctrine = TRUE;
            }

            if ($hasDoctrine && $hasWildfire) {
                $this->message = 'Please select "--wildfire" or "--doctrine"!';
            } else if ($hasDoctrine) {
                $this->type = 'doctrine';
            } else if ($hasWildfire) {
                $this->type = 'wildfire';
            } else {
                $this->message = 'Please install Wildfire or Doctrine!';
            }
        } else if ($this->doesExists['doctrine']) {
            $this->type = 'doctrine';
        } else if ($this->doesExists['wildfire']) {
            $this->type = 'wildfire';
        }

        if ($this->type == 'wildfire' && $this->doesExists['camel']) {
            $this->message = 'Wildfire does not support camel casing!';
        }

        return $this->type;
    }

    /**
     * Check if validations receive some errors
     * 
     * @return boolean
     */
    public function hasError()
    {
        return !empty($this->message) ? TRUE : FALSE;
    }
}
