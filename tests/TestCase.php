<?php

namespace Rougin\Combustor;

use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;

class TestCase extends \PHPUnit_Framework_TestCase
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
        $this->path = __DIR__ . DIRECTORY_SEPARATOR . 'Application';
    }

    /**
     * Injects a command with its dependencies.
     *
     * @param  string $command
     * @return \Symfony\Component\Console\Command\Command
     */
    protected function buildCommand($command)
    {
        $injector = new \Auryn\Injector;

        $loader = new \Twig_Loader_Filesystem(__DIR__ . '/../src/Templates');
        $twig   = new \Twig_Environment($loader);

        $twig->addExtension(new \Rougin\Combustor\Common\InflectorExtension);

        $ci = \Rougin\SparkPlug\Instance::create($this->path);

        $ci->load->helper('inflector')->database();

        $database = (array) $ci->db;

        if (strpos($database['dsn'], 'sqlite') !== false) {
            $database['hostname'] = $database['dsn'];
        }

        $driver   = new \Rougin\Describe\Driver\CodeIgniterDriver($database);
        $describe = new \Rougin\Describe\Describe($driver);

        $adapter    = new \League\Flysystem\Adapter\Local($this->path);
        $filesystem = new \League\Flysystem\Filesystem($adapter);

        $injector->share($describe)->share($filesystem)->share($twig);

        return $injector->make($command);
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
        $directory = new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS);
        $iterator  = new \RecursiveIteratorIterator($directory, \RecursiveIteratorIterator::CHILD_FIRST);

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

    /**
     * Sets default configurations.
     *
     * @return void
     */
    protected function setDefaults()
    {
        $application = $this->path . '/application';

        $autoload = file_get_contents($application . '/config/default/autoload.php');
        $config   = file_get_contents($application . '/config/default/config.php');

        file_put_contents($application . '/config/autoload.php', $autoload);
        file_put_contents($application . '/config/config.php', $config);

        $this->emptyDirectory($application . '/controllers');
        $this->emptyDirectory($application . '/models');
        $this->emptyDirectory($application . '/views');

        $layouts = $application . '/views/layout';

        ! is_dir($layouts) || $this->emptyDirectory($layouts, true);
    }
}
