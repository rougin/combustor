<?php

namespace Rougin\Combustor\Generator;

use Rougin\Describe\Describe;

/**
 * Base Generator
 *
 * @package Combustor
 * @author  Rougin Gutib <rougingutib@gmail.com>
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
     * Gets the primary keys based on specified field.
     *
     * @param  array  $data
     * @param  string $field
     * @param  string $referencedTable
     * @return array
     */
    protected function getPrimaryKey(array $data, $field, $referencedTable)
    {
        $accessor = 'get_' . $this->describe->getPrimaryKey($referencedTable);

        $data['primaryKeys'][$field] = $accessor;

        if ($data['isCamel']) {
            $camelized = camelize($data['primaryKeys'][$field]);

            $data['primaryKeys'][$field] = $camelized;
        }

        return $data;
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
                'accessor' => lcfirst(camelize('get_' . $field)),
                'mutator' => lcfirst(camelize('set_' . $field))
            ];
        }

        return [
            'field' => lcfirst(underscore($field)),
            'accessor' => lcfirst(underscore('get_' . $field)),
            'mutator' => lcfirst(underscore('set_' . $field))
        ];
    }
}
