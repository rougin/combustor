<?php

namespace Rougin\Combustor\Generator;

use Rougin\Describe\Describe;

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
                'field' => lcfirst(camelize($field)),
                'accessor' => lcfirst(camelize('set_' . $field)),
                'mutator' => lcfirst(camelize('get_' . $field))
            ];
        }

        return [
            'field' => lcfirst(underscore($field)),
            'accessor' => lcfirst(underscore('set_' . $field)),
            'mutator' => lcfirst(underscore('get_' . $field))
        ];
    }
}
