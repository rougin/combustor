<?php

namespace Rougin\Combustor;

/**
 * A utility extracted from Codeigniter's Inflector helper.
 *
 * @package Combustor
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class Inflector
{
    /**
     * Takes a singular word and makes it plural
     *
     * @param  string $word
     * @return string
     */
    public static function plural($word)
    {
        $result = strval($word);

        if ( ! word_is_countable($result))
        {
            return $result;
        }

        $rule = array(
            '/(quiz)$/'                => '\1zes',      // quizzes
            '/^(ox)$/'                 => '\1\2en',     // ox
            '/([m|l])ouse$/'           => '\1ice',      // mouse, louse
            '/(matr|vert|ind)ix|ex$/'  => '\1ices',     // matrix, vertex, index
            '/(x|ch|ss|sh)$/'          => '\1es',       // search, switch, fix, box, process, address
            '/([^aeiouy]|qu)y$/'       => '\1ies',      // query, ability, agency
            '/(hive)$/'                => '\1s',        // archive, hive
            '/(?:([^f])fe|([lr])f)$/'  => '\1\2ves',    // half, safe, wife
            '/sis$/'                   => 'ses',        // basis, diagnosis
            '/([ti])um$/'              => '\1a',        // datum, medium
            '/(p)erson$/'              => '\1eople',    // person, salesperson
            '/(m)an$/'                 => '\1en',       // man, woman, spokesman
            '/(c)hild$/'               => '\1hildren',  // child
            '/(buffal|tomat)o$/'       => '\1\2oes',    // buffalo, tomato
            '/(bu|campu)s$/'           => '\1\2ses',    // bus, campus
            '/(alias|status|virus)$/'  => '\1es',       // alias
            '/(octop)us$/'             => '\1i',        // octopus
            '/(ax|cris|test)is$/'      => '\1es',       // axis, crisis
            '/s$/'                     => 's',          // no change (compatibility)
            '/$/'                      => 's',
        );

        foreach ($rule as $rule => $replace)
        {
            if (preg_match($rule, $result))
            {
                $result = preg_replace($rule, $replace, $result);

                break;
            }
        }

        return $result;
    }
}