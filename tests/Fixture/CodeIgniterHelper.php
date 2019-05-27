<?php

namespace Rougin\Combustor\Fixture;

use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

/**
 * CodeIgniter Helper
 *
 * @package Combustor
 * @author  Rougin Gutib <rougingutib@gmail.com>
 */
class CodeIgniterHelper
{
    /**
     * Sets default configurations.
     *
     * @param  string $appPath
     * @return void
     */
    public static function setDefaults($appPath)
    {
        $files = [
            [
                'source' => $appPath . '/config/default/autoload.php',
                'destination' => $appPath . '/config/autoload.php'
            ],
            [
                'source' => $appPath . '/config/default/config.php',
                'destination' => $appPath . '/config/config.php'
            ],
        ];

        foreach ($files as $file) {
            $contents = file_get_contents($file['source']);

            file_put_contents($file['destination'], $contents);
        }

        self::emptyDirectory($appPath . '/controllers');
        self::emptyDirectory($appPath . '/models');
        self::emptyDirectory($appPath . '/views');

        $files = [
            $appPath . '/config/pagination.php',
            $appPath . '/libraries/Doctrine.php',
            $appPath . '/libraries/Wildfire.php',
            $appPath . '/views/layout/header.php',
            $appPath . '/views/layout/footer.php',
            '.htaccess',
        ];

        foreach ($files as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }

        $directories = [
            $appPath . '/views/layout',
            $appPath . '/models/proxies',
            'bower_components',
            'vendor/doctrine/orm'
        ];

        foreach ($directories as $directory) {
            if (is_dir($directory)) {
                self::emptyDirectory($directory, true);
            }
        }
    }

    /**
     * Deletes files in the specified directory.
     *
     * @param  string  $directory
     * @param  boolean $delete
     * @return void
     */
    protected static function emptyDirectory($directory, $delete = false)
    {
        $it = new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);

        foreach ($files as $file) {
            $isErrorDirectory = strpos($file->getRealPath(), 'errors');
            $isIndexHtml = strpos($file->getRealPath(), 'index.html');

            if ($isErrorDirectory !== false || $isIndexHtml !== false) {
                continue;
            }

            if ($file->isDir()) {
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }

        if ($delete) {
            rmdir($directory);
        }
    }
}
