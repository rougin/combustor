<?php

namespace Rougin\Combustor\Generator;

use Rougin\Describe\Describe;
use Rougin\Combustor\Common\Inflector;

/**
 * Base Generator
 * 
 * @package Combustor
 * @author  Rougin Royce Gutib <rougingutib@gmail.com>
 */
class BaseGenerator
{
    /**
     * @var \Rougin\Describe\Describe
     */
    protected $describe;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @param \Rougin\Describe\Describe $describe
     * @param array $data
     */
    public function __construct(Describe $describe, array $data)
    {
        $this->describe = $describe;
        $this->data = $data;
    }

    /**
     * Transforms the field into the template.
     * 
     * @param  string $field
     * @param  string $type
     * @return array
     */
    protected function transformField($field, $type)
    {
        if ($type == 'camelize') {
            return [
                'field' => lcfirst(Inflector::camelize($field)),
                'accessor' => lcfirst(Inflector::camelize('set_' . $field)),
                'mutator' => lcfirst(Inflector::camelize('get_' . $field))
            ];
        }

        return [
            'field' => lcfirst(Inflector::underscore($field)),
            'accessor' => lcfirst(Inflector::underscore('set_' . $field)),
            'mutator' => lcfirst(Inflector::underscore('get_' . $field))
        ];
    }
}
