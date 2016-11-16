<?php

namespace Rougin\Combustor;

class TestCase extends \PHPUnit_Framework_Testcase
{
    /**
     * @var string
     */
    protected $path;

    /**
     * Sets up the command and the application path.
     *
     * @return void
     */
    public function setUp()
    {
        $this->path = __DIR__ . '/../TestApp/application';
    }

    /**
     * Sets default configurations.
     *
     * @return void
     */
    protected function setDefaults()
    {
        $autoload = file_get_contents($this->path . '/config/default/autoload.php');
        $config   = file_get_contents($this->path . '/config/default/config.php');

        file_put_contents($this->path . '/config/autoload.php', $autoload);
        file_put_contents($this->path . '/config/config.php', $config);

        $this->emptyDirectory($this->path . '/controllers');
        $this->emptyDirectory($this->path . '/models');
        $this->emptyDirectory($this->path . '/views');

        $layouts = $this->path . '/views/layout';

        ! is_dir($layouts) || $this->emptyDirectory($layouts, true);
    }

    /**
     * Deletes files in the specified directory.
     *
     * @param  string  $directory
     * @param  boolean $delete
     * @return void
     */
    protected function emptyDirectory($directory, $delete = false)
    {
        $directory = new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS);
        $iterator  = new RecursiveIteratorIterator($directory, RecursiveIteratorIterator::CHILD_FIRST);

        foreach ($iterator as $file) {
            $isErrorDirectory = strpos($file->getRealPath(), 'errors');
            $isIndexHtmlFile  = strpos($file->getRealPath(), 'index.html');

            if ($isErrorDirectory !== false || $isIndexHtmlFile !== false) {
                continue;
            }

            $file->isDir() ? rmdir($file->getRealPath()) : unlink($file->getRealPath());
        }

        ! $delete || rmdir($directory);
    }
}
