<?php

namespace Combustor\Tools;

/**
 * Inflect Class for PHP
 *
 * The original author of this library is from Oodle
 * 
 * @author  mmucklo
 * @link    https://github.com/oodle/inflect
 */

class Inflect
{

    /**
     * Regular expressions for plural words
     */
    private static $plural = array(
        '/(quiz)$/i'               => "$1zes",
        '/^(oxen)$/i'              => "$1",
        '/^(ox)$/i'                => "$1en",
        '/([m|l])ice$/i'           => "$1ice",
        '/([m|l])ouse$/i'          => "$1ice",
        '/(matr|vert|ind)ix|ex$/i' => "$1ices",
        '/(x|ch|ss|sh)$/i'         => "$1es",
        '/([^aeiouy]|qu)y$/i'      => "$1ies",
        '/(hive)$/i'               => "$1s",
        '/(?:([^f])fe|([lr])f)$/i' => "$1$2ves",
        '/(shea|lea|loa|thie)f$/i' => "$1ves",
        '/sis$/i'                  => "ses",
        '/([ti])a$/i'              => "$1a",
        '/([ti])um$/i'             => "$1a",
        '/(buffal|tomat|potat|ech|her|vet)o$/i'=> "$1oes",
        '/(bu)s$/i'                => "$1ses",
        '/(alias|status)$/i'       => "$1es",
        '/(octop|vir)i$/i'         => "$1i",
        '/(octop|vir)us$/i'        => "$1i",
        '/(ax|test)is$/i'          => "$1es",
        '/(us)$/i'                 => "$1es",
        '/s$/i'                    => "s",
        '/$/'                      => "s"
    );
    
    /**
     * Regular expressions for singular words
     */
    private static $singular = array(
        '/(matr)ices$/'     => '\1ix',
        '/(vert|ind)ices$/' => '\1ex',
        '/^(ox)en/'     => '\1',
        '/(alias)es$/'      => '\1',
        '/([octop|vir])i$/' => '\1us',
        '/(cris|ax|test)es$/'   => '\1is',
        '/(shoe)s$/'        => '\1',
        '/(o)es$/'      => '\1',
        '/(bus|campus)es$/' => '\1',
        '/([m|l])ice$/'     => '\1ouse',
        '/(x|ch|ss|sh)es$/' => '\1',
        '/(m)ovies$/'       => '\1\2ovie',
        '/(s)eries$/'       => '\1\2eries',
        '/([^aeiouy]|qu)ies$/'  => '\1y',
        '/([lr])ves$/'      => '\1f',
        '/(tive)s$/'        => '\1',
        '/(hive)s$/'        => '\1',
        '/([^f])ves$/'      => '\1fe',
        '/(^analy)ses$/'    => '\1sis',
        '/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/' => '\1\2sis',
        '/([ti])a$/'        => '\1um',
        '/(p)eople$/'       => '\1\2erson',
        '/(m)en$/'      => '\1an',
        '/(s)tatuses$/'     => '\1\2tatus',
        '/(c)hildren$/'     => '\1\2hild',
        '/(n)ews$/'     => '\1\2ews',
        '/([^us])s$/'       => '\1'
    );

    /**
     * Irregular singular and plural forms
     */
    private static $irregular = array(
        'zombie' => 'zombies',
        'move'   => 'moves',
        'cow'    => 'kine',
        'foot'   => 'feet',
        'goose'  => 'geese',
        'sex'    => 'sexes',
        'child'  => 'children',
        'man'    => 'men',
        'tooth'  => 'teeth',
        'person' => 'people'
    );

    /**
     * Words that cannot be inflected
     */
    static $uncountable = array(
        'sheep'       => true,
        'fish'        => true,
        'deer'        => true,
        'series'      => true,
        'species'     => true,
        'money'       => true,
        'rice'        => true,
        'information' => true,
        'equipment'   => true,
        'jeans'       => true,
        'police'      => true,
        'fecalysis'   => true
    );

    /**
     * Storage for the singular and plural forms
     */
    private static $pluralCache = array();
    private static $singularCache = array();

    /**
     * Takes multiple words separated by spaces or underscores and camelizes them
     *
     * @param   string  $string     Input string
     * @return  string
     */
    public static function camelize($string)
    {
        return strtolower($string[0]).substr(str_replace(' ', '', ucwords(preg_replace('/[\s_]+/', ' ', $string))), 1);
    }

    /**
     * Takes multiple words separated by the separator and changes them to spaces
     *
     * @param   string  $string        Input string
     * @param   string  $separator  Input separator
     * @return  string
     */
    public static function humanize($string, $separator = '_')
    {
        return ucwords(preg_replace('/[' . $separator . ']+/', ' ', trim(strtolower($string))));
    }

    /**
     * Pluralize a specified string
     * 
     * @param  string $string   The string to be pluralized
     * @return array
     */
    public static function pluralize($string)
    {
        if ( ! $string) return;

        if ( ! isset(self::$pluralCache[$string])) {
            /**
             * Save some time in the case that singular and plural are the same
             */
            
            if (isset(self::$uncountable[$string])) {
                self::$pluralCache[$string] = $string;

                return $string;
            }

            /**
             * Check for irregular singular forms
             */
            
            foreach (self::$irregular as $pattern => $result) {
                $pattern = '/' . $pattern . '$/i';

                if (preg_match($pattern, $string)) {
                    self::$pluralCache[$string] = preg_replace($pattern, $result, $string);

                    return self::$pluralCache[$string];
                }
            }

            /**
             * Check for matches using regular expressions
             */
            
            foreach (self::$plural as $pattern => $result) {
                if (preg_match($pattern, $string)) {
                    self::$pluralCache[$string] = $result = preg_replace($pattern, $result, $string);

                    return self::$pluralCache[$string];
                }
            }

            self::$pluralCache[$string] = $string;
        }

        return self::$pluralCache[$string];
    }

    /**
     * Pluralize the specified string with a condition
     * 
     * @param  int      $count
     * @param  string   $string     The string to be pluralized
     * @return array
     */
    public static function pluralizeIf($count, $string)
    {
        return ($count == 1) ? '1 $string' : '$count ' . self::pluralize($string);
    }

    /**
     * Singularize a specified string
     * 
     * @param  string $string   The string to be singularized
     * @return array
     */
    public static function singularize($string)
    {
        if ( ! $string) return;

        if ( ! isset(self::$singularCache[$string])) {
            /**
             * Save some time in the case that singular and plural are the same
             */
            
            if (isset(self::$uncountable[strtolower($string)])) {
                self::$singularCache[$string] = $string;
                return $string;
            }

            /**
             * Check for irregular plural forms
             */
            
            foreach (self::$irregular as $result => $pattern) {
                $pattern = '/' . $pattern . '$/i';

                if (preg_match($pattern, $string)) {
                    self::$singularCache[$string] = preg_replace($pattern, $result, $string);

                    return self::$singularCache[$string];
                }
            }

            /**
             * Check for matches using regular expressions
             */
            
            foreach (self::$singular as $pattern => $result) {
                if (preg_match($pattern, $string)) {
                    self::$singularCache[$string] = preg_replace($pattern, $result, $string);

                    return self::$singularCache[$string];
                }
            }

            self::$singularCache[$string] = $string;
        }

        return self::$singularCache[$string];
    }

    /**
     * Takes multiple words separated by spaces and underscores them
     *
     * @param   string  $string     Input string
     * @return  string
     */
    public static function underscore($string)
    {
        return preg_replace('/[\s]+/', '_', trim(strtolower($string)));
    }

}