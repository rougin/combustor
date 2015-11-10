<?php

namespace Rougin\Combustor\Common;

/**
 * File
 *
 * A simple object-oriented interface for handling files.
 * 
 * @package Combustor
 * @author  Rougin Royce Gutib <rougingutib@gmail.com>
 */
class File
{
    protected $file;
    protected $path;

    /**
     * @param string $path
     * @param string $mode
     */
    public function __construct($path, $mode)
    {
        $this->path = $path;
        $this->file = fopen($path, $mode);
    }

    /**
     * Closes an open file pointer.
     * 
     * @return boolean
     */
    public function close()
    {
        return fclose($this->file);
    }

    /**
     * Reads entire file into a string.
     * 
     * @return string
     */
    public function getContents()
    {
        return file_get_contents($this->path);
    }

    /**
     * Writes a string to a file.
     * 
     * @param  string $content
     * @return integer|boolean
     */
    public function putContents($content)
    {
        return file_put_contents($this->path, $content);
    }

    /**
     * Changes the file mode of the file.
     * 
     * @param  int $mode
     * @return boolean
     */
    public function chmod(int $mode)
    {
        return chmod($this->path, $mode);
    }
}
