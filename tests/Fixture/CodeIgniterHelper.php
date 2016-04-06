<?php

namespace Rougin\Combustor\Fixture;

use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

/**
 * CodeIgniter Helper
 * 
 * @package Combustor
 * @author  Rougin Royce Gutib <rougingutib@gmail.com>
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

        $files = [
            $appPath . '/config/pagination.php',
            $appPath . '/controllers/Users.php',
            $appPath . '/libraries/Doctrine.php',
            $appPath . '/libraries/Wildfire.php',
            $appPath . '/models/Users.php',
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
            $appPath . '/views/users',
            $appPath . '/views/users',
            $appPath . '/models/proxies',
            'bower_components'
        ];

        foreach ($directories as $directory) {
            if (is_dir($directory)) {
                self::deleteDirectory($directory);
            }
        }
    }

    /**
     * Deletes a specified directory with its files.
     * 
     * @param  string $directory
     * @return void
     */
    public static function deleteDirectory($directory)
    {
        $it = new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);

        foreach ($files as $file) {
            if ($file->isDir()) {
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }

        rmdir($directory);
    }
}
