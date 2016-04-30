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
        // Gets data from application/config/config.php
        $config = file_get_contents(APPPATH . 'config/config.php');

        $search = ['$config[\'composer_autoload\'] = FALSE;'];
        $replace = ['$config[\'composer_autoload\'] = realpath(\'vendor\') . \'/autoload.php\';'];

        // Replaces configuration found in config.php
        $configs = [
            [
                'search' => '$config[\'index_page\'] = \'index.php\';',
                'replacement' => '$config[\'index_page\'] = \'\';'
            ],
            [
                'search' => '$config[\'encryption_key\'] = \'\';',
                'replacement' => '$config[\'encryption_key\'] = md5(\'rougin\');'
            ]
        ];

        foreach ($configs as $row) {
            if (strpos($config, $row['search']) !== false) {
                array_push($search, $row['search']);
                array_push($replace, $row['replacement']);
            }
        }

        $config = str_replace($search, $replace, $config);
        file_put_contents(APPPATH . 'config/config.php', $config);

        // Gets data from application/config/autoload.php
        $autoload = file_get_contents(APPPATH . 'config/autoload.php');
        $lines = explode(PHP_EOL, $autoload);

        // Gets the currently included libraries.
        $pattern = '/\$autoload\[\'libraries\'\] = array\((.*?)\)/';

        preg_match_all($pattern, $lines[60], $match);

        $libraries = explode(', ', end($match[1]));

        // Includes "session" library.
        if ( ! in_array('\'session\'', $libraries)) {
            array_push($libraries, '\'session\'');
        }

        $libraries = array_filter($libraries);

        // Includes the added libraries all back to autoload.php.
        $pattern = '/\$autoload\[\'libraries\'\] = array\([^)]*\);/';
        $replacement = '$autoload[\'libraries\'] = array(' . implode(', ', $libraries) . ');';

        $lines[60] = preg_replace($pattern, $replacement, $lines[60]);

        // Gets the currently included helpers
        $pattern = '/\$autoload\[\'helper\'\] = array\((.*?)\)/';

        preg_match_all($pattern, $lines[85], $match);

        $defaultHelpers = [ '\'form\'', '\'url\'', ];
        $helpers = explode(', ', end($match[1]));

        foreach ($defaultHelpers as $helper) {
            if ( ! in_array($helper, $helpers)) {
                array_push($helpers, $helper);
            }
        }

        $helpers = array_filter($helpers);

        // Include the added helpers all back to autoload.php
        $pattern = '/\$autoload\[\'helper\'\] = array\([^)]*\);/';
        $replacement = '$autoload[\'helper\'] = array(' . implode(', ', $helpers) . ');';

        preg_replace($pattern, $replacement, $lines[60]);

        file_put_contents(APPPATH . 'config/autoload.php', implode(PHP_EOL, $lines));

        // Creates a new .htaccess file if it does not exists.
        if ( ! file_exists('.htaccess')) {
            $template = __DIR__ . '/../Templates/Htaccess.template';

            $file = fopen('.htaccess', 'wb');
            $contents = file_get_contents($template);

            file_put_contents('.htaccess', $contents);
            chmod('.htaccess', 0777);
            fclose($file);
        }

        // Creates a configuration for the Pagination library.
        if ( ! file_exists(APPPATH . 'config/pagination.php')) {
            $pagination = APPPATH . 'config/pagination.php';
            $template = __DIR__ . '/../Templates/Pagination.template';

            $file = fopen($pagination, 'wb');
            $contents = file_get_contents($template);

            file_put_contents($pagination, $contents);
            chmod($pagination, 0664);
            fclose($file);
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
     * Removes the specified library in the application.
     * 
     * @param  string $type
     * @return string
     */
    public static function removeLibrary($type)
    {
        $autoload = file_get_contents(APPPATH . 'config/autoload.php');

        $lines = explode(PHP_EOL, $autoload);
        $pattern = '/\$autoload\[\'libraries\'\] = array\((.*?)\)/';

        preg_match_all($pattern, $lines[60], $match);

        $libraries = explode(', ', end($match[1]));

        if (in_array('\'' . $type . '\'', $libraries)) {
            $position = array_search('\'' . $type . '\'', $libraries);

            unset($libraries[$position]);

            $libraries = array_filter($libraries);

            $pattern = '/\$autoload\[\'libraries\'\] = array\([^)]*\);/';
            $replacement = '$autoload[\'libraries\'] = array(' . implode(', ', $libraries) . ');';

            $lines[60] = preg_replace($pattern, $replacement, $lines[60]);

            file_put_contents(APPPATH . 'config/autoload.php', implode(PHP_EOL, $lines));
        }

        if ($type == 'doctrine') {
            system('composer remove doctrine/orm');
        }

        unlink(APPPATH . 'libraries/' . ucfirst($type) . '.php');

        return ucfirst($type) . ' is now successfully removed!';
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
