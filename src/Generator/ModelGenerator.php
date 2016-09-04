<?php

namespace Rougin\Combustor\Generator;

use Rougin\Combustor\Common\Tools;

/**
 * Model Generator
 *
 * Generates CodeIgniter-based models.
 *
 * @package Combustor
 * @author  Rougin Royce Gutib <rougingutib@gmail.com>
 */
class ModelGenerator extends BaseGenerator implements GeneratorInterface
{
    /**
     * Prepares the data before generation.
     *
     * @param  array &$data
     * @return void
     */
    public function prepareData(array &$data)
    {
        $data['camel'] = [];
        $data['columns'] = [];
        $data['indexes'] = [];
        $data['primaryKeys'] = [];
        $data['underscore'] = [];
        $data['columns'] = $this->describe->getTable($data['name']);
        $data['primaryKey'] = $this->describe->getPrimaryKey($data['name']);
    }

    /**
     * Generates set of code based on data.
     *
     * @return array
     */
    public function generate()
    {
        $this->prepareData($this->data);

        foreach ($this->data['columns'] as $column) {
            $field = strtolower($column->getField());

            $this->data['camel'][$field] = $this->transformField(
                $field,
                'camelize'
            );

            $this->data['underscore'][$field] = $this->transformField(
                $field,
                'underscore'
            );

            if ($column->isForeignKey()) {
                $field = $column->getField();
                $referencedTable = $column->getReferencedTable();

                array_push($this->data['indexes'], $field);

                $this->data = $this->getPrimaryKey(
                    $this->data,
                    $field,
                    $referencedTable
                );
            }

            $column->setReferencedTable(
                Tools::stripTableSchema($column->getReferencedTable())
            );
        }

        return $this->data;
    }
}
