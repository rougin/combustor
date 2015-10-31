<?php

namespace Rougin\Combustor\Common;

class File
{
    protected $file;
    protected $path;

    public function __construct($path, $mode)
    {
        $this->path = $path;
        $this->file = fopen($path, $mode);
    }

    public function close()
    {
        return fclose($this->file);
    }

    public function putContents($content)
    {
        return file_put_contents($this->path, $content);
    }
}
