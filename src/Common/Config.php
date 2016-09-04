<?php

namespace Rougin\Combustor\Common;

/**
 * Config
 *
 * A simple object-oriented interface for handling CodeIgniter's configurations.
 *
 * @package Combustor
 * @author  Rougin Royce Gutib <rougingutib@gmail.com>
 */
class Config
{
    /**
     * @var string
     */
    protected $config;

    /**
     * @var string
     */
    public $fileName;

    /**
     * @var array
     */
    protected $lines;

    /**
     * @param string $config
     * @param string $configPath
     */
    public function __construct($config, $configPath)
    {
        $this->config = $config;
        $this->fileName = $configPath . '/' . $config . '.php';

        $content = file_get_contents($this->fileName);

        $this->lines = preg_split("/\\r\\n|\\r|\\n/", $content);
    }

    /**
     * Returns the specified value from the config item.
     *
     * @param  string       $item
     * @param  integer      $line
     * @param  string  $dataType
     * @return mixed
     */
    public function get($item, $line, $dataType)
    {
        $result = null;
        $value = null;

        switch ($dataType) {
            case 'array':
                $value = 'array\((.*?)\)';

                break;
            case 'boolean':
                $value = '(TRUE|FALSE)';

                break;
            case 'string':
                $value = '(.*?)';

                break;
        }

        $pattern = '/\$' . $this->config . '\[\'' . $item . '\'\] = ' . $value . ';/';

        preg_match_all($pattern, $this->lines[$line], $match);

        switch ($dataType) {
            case 'array':
                $result = array_filter($match[1]);
                $data = '';

                if (! empty($result[0])) {
                    $data = $result[0];
                }

                $result = array_filter(explode(',', $data));
                $length = count($result);

                for ($i = 0; $i < $length; $i++) {
                    $result[$i] = str_replace(['\'', '"'], '', trim($result[$i]));
                }

                break;
            case 'boolean':
                $result = $match[1][0] == 'TRUE';

                break;
            case 'string':
                $result = $match[1][0];

                if ($result == '\'\'') {
                    $result = null;
                }

                if ($result[0] == '\'' && $result[strlen($result) - 1] == '\'') {
                    $result = substr($result, 1, strlen($result) - 2);
                }

                break;
        }

        return $result;
    }

    /**
     * Sets an value to an item in the config.
     *
     * @param string  $item
     * @param integer $line
     * @param mixed   $value
     * @param string  $dataType
     * @param boolean $exact
     */
    public function set($item, $line, $value, $dataType, $exact = false)
    {
        $data = null;
        $format = null;

        switch ($dataType) {
            case 'array':
                $length = count($value);

                for ($i = 0; $i < $length; $i++) {
                    $value[$i] = '\'' . $value[$i] . '\'';
                }

                $data = 'array(' . implode(', ', $value) . ')';
                $format = 'array\([^)]*\)';

                break;
            case 'boolean':
                $data = ($value) ? 'TRUE' : 'FALSE';
                $format = '[^)]*';

                break;
            case 'string':
                $data = '\'' . $value . '\'';
                $format = '(.*?)';

                break;
        }

        if ($exact) {
            $data = $value;
        }

        $pattern = '/\$' . $this->config . '\[\'' . $item . '\'\] = ' . $format . ';/';
        $replacement = '$' . $this->config . '[\'' . $item . '\'] = ' . $data . ';';

        $result = preg_replace($pattern, $replacement, $this->lines[$line]);

        $this->lines[$line] = $result;
    }

    /**
     * Saves the current config.
     *
     * @return void
     */
    public function save()
    {
        file_put_contents($this->fileName, (string) $this);
    }

    /**
     * Returns the whole class into a string.
     *
     * @return string
     */
    public function __toString()
    {
        return implode(PHP_EOL, $this->lines);
    }
}
