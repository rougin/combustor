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
    protected $file;
    protected $isCamel;
    protected $isDoctrine;
    protected $isWildfire;
    protected $library;
    protected $message;

    /**
     * @param string $isDoctrine
     * @param string $isWildfire
     * @param string $isCamel
     * @param array  $file
     */
    public function __construct($isDoctrine, $isWildfire, $isCamel, $file)
    {
        $this->isWildfire = $isWildfire;
        $this->isDoctrine = $isDoctrine;
        $this->isCamel = $isCamel;
        $this->file = $file;
    }

    /**
     * Checks if the validator fails.
     * 
     * @return boolean
     */
    public function fails()
    {
        $hasDoctrine = file_exists(APPPATH . '/libraries/Doctrine.php');
        $hasWildfire = file_exists(APPPATH . '/libraries/Wildfire.php');

        if ( ! $hasWildfire && ! $hasDoctrine) {
            $this->message = 'Please install Wildfire or Doctrine!';

            return TRUE;
        }

        if ($hasWildfire && $hasDoctrine) {
            $this->message = 'Please select "--wildfire" or "--doctrine"!';

            return TRUE;
        }

        if ($this->isDoctrine && $hasDoctrine) {
            $this->library = 'doctrine';

            return FALSE;
        }

        if ($this->isWildfire && $hasWildfire) {
            if ($this->isCamel) {
                $this->message = 'Wildfire does not support camel casing!';

                return FALSE;
            }

            $this->library = 'wildfire';

            return FALSE;
        }

        if (file_exists($this->file['path'])) {
            $this->message = 'The "' . $this->file['name'] . '" ' .
                $this->file['type'] . ' already exists!';

            return FALSE;
        }

        return TRUE;
    }

    /**
     * Gets the rendered message
     * 
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Gets the selected library
     * 
     * @return string
     */
    public function getLibrary()
    {
        return $this->library;
    }
}
