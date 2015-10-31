<?php

namespace Rougin\Combustor\Validator;

use Rougin\Describe\Describe;
use Rougin\Combustor\Validator\ValidatorInterface;

/**
 * View Validator
 *
 * Checks if it is valid to generate view files.
 * 
 * @package Combustor
 * @author  Rougin Royce Gutib <rougingutib@gmail.com>
 */
class ViewValidator implements ValidatorInterface
{
    protected $name;
    protected $message;

    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Checks if the validator fails.
     * 
     * @return boolean
     */
    public function fails()
    {
        $filePath = APPPATH . 'views/' . $this->name;

        if (! @mkdir($filePath, 0775, TRUE)) {
            $this->message = 'The "' . $this->name .
                '" views folder already exists!';

            return TRUE;
        }
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
}
