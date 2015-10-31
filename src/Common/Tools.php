<?php

namespace Rougin\Combustor\Common;

/**
 * Tools
 *
 * Provides multi-purpose functions for Combustor
 * 
 * @package Combustor
 * @author  Rougin Royce Gutib <rougingutib@gmail.com>
 */
class Tools
{
    /**
     * Run the post installation process
     * 
     * @return void
     */
    public static function ignite()
    {
        // Get the config.php from application/config directory
        $config = file_get_contents('application/config/config.php');

        // Enable the Composer autoload for CodeIgniter
        $search  = '$config[\'composer_autoload\'] = FALSE;';
        $replace = '$config[\'composer_autoload\'] = realpath(\'vendor\') . \'/autoload.php\';';
        $config = str_replace($search, $replace, $config);

        // Updates config.php
        $file = fopen('application/config/config.php', 'wb');

        file_put_contents('application/config/config.php', $config);
        fclose($file);

        // Get the autoload.php from application/config directory
        $autoload = file_get_contents(APPPATH.'config/autoload.php');

        // Get currently included libraries
        preg_match_all(
            '/\$autoload\[\'libraries\'\] = array\((.*?)\)/',
            $autoload,
            $match
        );

        $libraries = explode(', ', end($match[1]));

        // Include "session" library
        if ( ! in_array('\'session\'', $libraries)) {
            array_push($libraries, '\'session\'');
        }

        $libraries = array_filter($libraries);

        // Include the added libraries all back to autoload.php
        $autoload = preg_replace(
            '/\$autoload\[\'libraries\'\] = array\([^)]*\);/',
            '$autoload[\'libraries\'] = array('.implode(', ', $libraries).');',
            $autoload
        );

        // Get currently included helpers
        preg_match_all(
            '/\$autoload\[\'helper\'\] = array\((.*?)\)/',
            $autoload,
            $match
        );

        $helpers = explode(', ', end($match[1]));

        // Include "form" helper
        if ( ! in_array('\'form\'', $helpers)) {
            array_push($helpers, '\'form\'');
        }

        // Include "inflector" helper
        if ( ! in_array('\'inflector\'', $helpers)) {
            array_push($helpers, '\'inflector\'');
        }

        // Include "url" helper
        if ( ! in_array('\'url\'', $helpers)) {
            array_push($helpers, '\'url\'');
        }

        $helpers = array_filter($helpers);

        // Include the added helpers all back to autoload.php
        $autoload = preg_replace(
            '/\$autoload\[\'helper\'\] = array\([^)]*\);/',
            '$autoload[\'helper\'] = array('.implode(', ', $helpers).');',
            $autoload
        );

        // Updates autoload.php
        $file = fopen(APPPATH.'config/autoload.php', 'wb');

        file_put_contents(APPPATH.'config/autoload.php', $autoload);
        fclose($file);

        // Creates a .htaccess file if it does not exists
        if ( ! file_exists('.htaccess')) {
            $htaccess = fopen('.htaccess', 'wb');
            chmod('.htaccess', 0777);

            file_put_contents('.htaccess', $htaccess);
            fclose($htaccess);
        }

        // Gets the content of config.php
        $config = file_get_contents(APPPATH.'config/config.php');

        $search = array();
        $replace = array();

        // Removes the index.php from $config['index_page']
        if (strpos($config, '$config[\'index_page\'] = \'index.php\';') !== FALSE) {
            $search[] = '$config[\'index_page\'] = \'index.php\';';
            $replace[] = '$config[\'index_page\'] = \'\';';
        }

        // Adds an encryption key from the configuration
        if (strpos($config, '$config[\'encryption_key\'] = \'\';') !== FALSE) {
            $search[] = '$config[\'encryption_key\'] = \'\';';
            $replace[] = '$config[\'encryption_key\'] = \''.md5('rougin').'\';';
        }

        // Updates the config.php
        $config = str_replace($search, $replace, $config);
        file_put_contents(APPPATH.'config/config.php', $config);

        // Add an autoload for the Pagination library in applications/config/pagination.php
        if ( ! file_exists(APPPATH.'config/pagination.php')) {
            $pagination = fopen(APPPATH.'config/pagination.php', 'wb');
            chmod(APPPATH.'config/pagination.php', 0664);

            file_put_contents(
                APPPATH.'config/pagination.php',
                file_get_contents(
                    __DIR__.'/Templates/Pagination.php'
                )
            );

            fclose($pagination);
        }

        return;
    }

    /**
     * Checks whether the command is enabled or not in the current environment.
     *
     * @return bool
     */
    public static function isCommandEnabled()
    {
        if (
            file_exists(APPPATH.'libraries/Wildfire.php') ||
            file_exists(APPPATH.'libraries/Doctrine.php')
        ) {
            return TRUE;
        }

        return FALSE;
    }

    /**
     * Checks if Doctrine exists.
     *
     * @return bool
     */
    public static function isDoctrineEnabled()
    {
        if ( ! file_exists(APPPATH.'libraries/Doctrine.php')) {
            return FALSE;
        }

        return TRUE;
    }

    /**
     * Checks whether the header and footer file exists.
     *
     * @return bool
     */
    public static function hasLayout()
    {
        if (
            file_exists(APPPATH.'views/layout/header.php') &&
            file_exists(APPPATH.'views/layout/footer.php')
        ) {
            return FALSE;
        }

        return TRUE;
    }

    /**
     * Checks if Wildfire exists.
     *
     * @return bool
     */
    public static function isWildfireEnabled()
    {
        if ( ! file_exists(APPPATH.'libraries/Wildfire.php')) {
            return FALSE;
        }

        return TRUE;
    }

    /**
     * Strip the table schema from the table name
     * 
     * @param  string $table
     * @return string
     */
    public static function stripTableSchema($table)
    {
        if (strpos($table, '.') !== FALSE) {
            return substr($table, strpos($table, '.') + 1);
        }

        return $table;
    }

    /**
     * Strip the table schema from the table name
     * 
     * @param  string $table
     * @return string
     */
    public static function strip_table_schema($table)
    {
        return self::stripTableSchema($table);
    }
}
