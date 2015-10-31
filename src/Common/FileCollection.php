<?php

namespace Rougin\Combustor\Common;

class FileCollection
{
    protected $files;

    public function add(File $file, $key)
    {
        $this->files[$key] = $file;

        return $this;
    }

    public function close()
    {
        foreach ($this->files as $file) {
            if ( ! $file->close()) {
                return FALSE;
            }
        }

        return TRUE;
    }

    public function &get($key)
    {
        return $this->files[$key];
    }
}