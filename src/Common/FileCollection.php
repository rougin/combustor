<?php

namespace Rougin\Combustor\Common;

use Rougin\Combustor\Common\File;

/**
 * File Collection
 *
 * A simple object-oriented interface for handling File objects.
 * 
 * @package Combustor
 * @author  Rougin Royce Gutib <rougingutib@gmail.com>
 */
class FileCollection
{
    protected $files;

    /**
     * Adds a File object to the listing of files.
     * 
     * @param File   $file
     * @param string $key
     */
    public function add(File $file, $key)
    {
        $this->files[$key] = $file;

        return $this;
    }

    /**
     * Closes all File objects.
     * 
     * @return boolean
     */
    public function close()
    {
        foreach ($this->files as $file) {
            if ( ! $file->close()) {
                return FALSE;
            }
        }

        return TRUE;
    }

    /**
     * Gets a specified File object.
     * 
     * @param  string $key
     * @return File
     */
    public function &get($key)
    {
        return $this->files[$key];
    }
}
