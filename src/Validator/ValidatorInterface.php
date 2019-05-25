<?php

namespace Rougin\Combustor\Validator;

/**
 * Validator Interface
 *
 * An interface for validating generators.
 *
 * @package Combustor
 * @author  Rougin Gutib <rougingutib@gmail.com>
 */
interface ValidatorInterface
{
    /**
     * Checks if the validator fails.
     *
     * @return boolean
     */
    public function fails();

    /**
     * Gets the rendered message.
     *
     * @return string
     */
    public function getMessage();
}
