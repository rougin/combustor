<?php

namespace Rougin\Combustor\Common;

/**
 * Tools
 *
 * Provides a list of multi-purpose functions for Combustor.
 * 
 * @package Combustor
 * @author  Rougin Royce Gutib <rougingutib@gmail.com>
 */
class Tools
{
    /**
     * Checks whether the header and footer file exists.
     *
     * @return bool
     */
    public static function hasLayout()
    {
        $header = APPPATH . 'views/layout/header.php';
        $footer = APPPATH . 'views/layout/footer.php';

        return file_exists($header) && file_exists($footer);
    }

    /**
     * "Ignites" the post installation process.
     * 
     * @return void
     */
    public static function ignite()
    {
        $autoloadPath = 'realpath(\'vendor\') . \'/autoload.php\'';
        $configPath = APPPATH . 'config';
        $templatePath = __DIR__ . '/../Templates';

        // Gets data from application/config/config.php
        $config = new Config('config', $configPath);

        $config->set('composer_autoload', 138, $autoloadPath, 'string', true);
        $config->set('index_page', 37, '', 'string');
        $config->set('encryption_key', 316, md5('rougin'), 'string');

        $config->save();

        // Gets data from application/config/autoload.php
        $autoload = new Config('autoload', $configPath);

        // Gets the currently included drivers.
        $drivers = $autoload->get('drivers', 81, 'array');

        // Includes "session" driver.
        if ( ! in_array('session', $drivers)) {
            array_push($drivers, 'session');
        }

        // Gets the currently included helpers
        $defaultHelpers = [ 'form', 'url' ];
        $helpers = $autoload->get('helper', 91, 'array');

        foreach ($defaultHelpers as $helper) {
            if ( ! in_array($helper, $helpers)) {
                array_push($helpers, $helper);
            }
        }

        $autoload->set('drivers', 81, $drivers, 'array');
        $autoload->set('helper', 91, $helpers, 'array');

        $autoload->save();

        $templates = [
            [
                'file' => '.htaccess',
                'name' => 'Htaccess'
            ],
            [
                'file' => APPPATH . 'config/pagination.php',
                'name' => 'Pagination'
            ]
        ];

        foreach ($templates as $template) {
            if ( ! file_exists($template['file'])) {
                $file = new File($template['file']);

                $path = $templatePath . '/' . $template['name'] . '.template';
                $contents = file_get_contents($path);

                $file->putContents($contents);
                $file->close();
            }
        }
    }

    /**
     * Checks whether the command is enabled or not in the current environment.
     *
     * @return bool
     */
    public static function isCommandEnabled()
    {
        return self::isWildfireEnabled() || self::isDoctrineEnabled();
    }

    /**
     * Checks if Doctrine exists.
     *
     * @return bool
     */
    public static function isDoctrineEnabled()
    {
        return file_exists(APPPATH . 'libraries/Doctrine.php');
    }

    /**
     * Checks if Wildfire exists.
     *
     * @return bool
     */
    public static function isWildfireEnabled()
    {
        return file_exists(APPPATH . 'libraries/Wildfire.php');
    }

    /**
     * Strips the table schema from the table name.
     * 
     * @param  string $table
     * @return string
     */
    public static function stripTableSchema($table)
    {
        return (strpos($table, '.') !== false)
            ? substr($table, strpos($table, '.') + 1)
            : $table;
    }

    /**
     * Strips the table schema from the table name.
     * 
     * @param  string $table
     * @return string
     */
    public static function strip_table_schema($table)
    {
        return self::stripTableSchema($table);
    }
}
