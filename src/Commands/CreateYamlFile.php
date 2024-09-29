<?php

namespace Rougin\Combustor\Commands;

use Rougin\Blueprint\Commands\InitializeCommand;

/**
 * @package Combustor
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class CreateYamlFile extends InitializeCommand
{
    /**
     * @var string
     */
    protected $file = 'combustor.yml';

    /**
     * Returns the source directory for the specified file.
     *
     * @return string
     */
    protected function getPlatePath()
    {
        /** @var string */
        return realpath(__DIR__ . '/../Template');
    }

    /**
     * Returns the root directory from the package.
     *
     * @return string
     */
    protected function getRootPath()
    {
        /** @var string */
        $vendor = realpath(__DIR__ . '/../../../../../');

        $exists = file_exists($vendor . '/../autoload.php');

        return $exists ? $vendor : __DIR__ . '/../../';
    }
}
