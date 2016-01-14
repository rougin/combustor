<?php

namespace Rougin\Combustor\Validator;

use Rougin\Describe\Describe;
use Rougin\Combustor\Validator\ValidatorInterface;

/**
 * Validator
 *
 * Checks if Wildfire or Doctrine is available
 * 
 * @package Combustor
 * @author  Rougin Royce Gutib <rougingutib@gmail.com>
 */
class Validator implements ValidatorInterface
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
     * @var boolean
     */
    protected $isDoctrine = false;

    /**
     * @var boolean
     */
    protected $isWildfire = false;

    /**
     * @var string
     */
    protected $library = '';

    /**
     * @var string
     */
    protected $message = '';

    /**
     * @param boolean $isDoctrine
     * @param boolean $isWildfire
     * @param boolean $isCamel
     * @param array   $file
     */
    public function __construct($isDoctrine, $isWildfire, $isCamel, $file)
    {
        $this->file = $file;
        $this->isCamel = $isCamel;
        $this->isWildfire = $isWildfire;
        $this->isDoctrine = $isDoctrine;
    }

    /**
     * Checks if the validator fails.
     * 
     * @return boolean
     */
    public function fails()
    {
        $hasDoctrine = file_exists(APPPATH.'libraries/Doctrine.php');
        $hasWildfire = file_exists(APPPATH.'libraries/Wildfire.php');

        if ( ! $hasWildfire && ! $hasDoctrine) {
            $this->message = 'Please install Wildfire or Doctrine!';

            return true;
        }

        if ($hasWildfire && $hasDoctrine) {
            $this->message = 'Please select "--wildfire" or "--doctrine"!';

            return true;
        }

        if ($this->isDoctrine || $hasDoctrine) {
            $this->library = 'doctrine';

            return false;
        }

        if ($this->isWildfire || $hasWildfire) {
            if ($this->isCamel) {
                $this->message = 'Wildfire does not support camel casing!';

                return true;
            }

            $this->library = 'wildfire';

            return false;
        }

        if (file_exists($this->file['path'])) {
            $this->message = 'The "'.$this->file['name'].'" '.
                $this->file['type'].' already exists!';

            return true;
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
