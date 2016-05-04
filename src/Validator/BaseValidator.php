<?php

namespace Rougin\Combustor\Validator;

/**
 * Base Validator
 *
 * Checks if Wildfire or Doctrine is available
 * 
 * @package Combustor
 * @author  Rougin Royce Gutib <rougingutib@gmail.com>
 */
class BaseValidator implements ValidatorInterface
{
    /**
     * @var array
     */
    protected $file = [];

    /**
     * @var boolean
     */
    protected $isCamel = false;

    /**
     * @var string
     */
    protected $library = '';

    /**
     * @var string
     */
    protected $message = '';

    /**
     * @param boolean $isCamel
     * @param array   $file
     */
    public function __construct($isCamel, $file)
    {
        $this->file = $file;
        $this->isCamel = $isCamel;
    }

    /**
     * Checks if the validator fails.
     * 
     * @return boolean
     */
    public function fails()
    {
        $hasDoctrine = file_exists(APPPATH . 'libraries/Doctrine.php');
        $hasWildfire = file_exists(APPPATH . 'libraries/Wildfire.php');

        if ( ! $hasWildfire && ! $hasDoctrine) {
            $this->message = 'Please install Wildfire or Doctrine!';

            return true;
        }

        if ($hasWildfire && $hasDoctrine) {
            $this->message = 'Both Wildfire and Doctrine exists! Choose only one.';

            return true;
        }

        if ($hasWildfire && $this->isCamel) {
            $this->message = 'Wildfire does not support camel casing!';

            return true;
        }

        if (file_exists($this->file['path'])) {
            $name = $this->file['name'];
            $type = $this->file['type'];

            $this->message = 'The "' . $name . '" ' . $type . ' already exists!';

            return true;
        }

        if ($hasDoctrine) {
            $this->library = 'doctrine';
        } else {
            $this->library = 'wildfire';
        }

        return false;
    }

    /**
     * Gets the rendered message.
     * 
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Gets the selected library.
     * 
     * @return string
     */
    public function getLibrary()
    {
        return $this->library;
    }
}
