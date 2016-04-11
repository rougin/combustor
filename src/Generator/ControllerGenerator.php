<?php

namespace Rougin\Combustor\Generator;

use Rougin\Combustor\Common\Tools;
use Rougin\Combustor\Common\Inflector;

/**
 * Controller Generator
 *
 * Generates CodeIgniter-based controllers.
 * 
 * @package Combustor
 * @author  Rougin Royce Gutib <rougingutib@gmail.com>
 */
class ControllerGenerator extends BaseGenerator implements GeneratorInterface
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
        $data['dropdowns'] = [];
        $data['foreignKeys'] = [];
        $data['models'] = [$data['name']];
        $data['plural'] = Inflector::plural($data['name']);
        $data['singular'] = Inflector::singular($data['name']);
        $data['underscore'] = [];
    }

    /**
     * Generates set of code based on data.
     * 
     * @return array
     */
    public function generate()
    {
        $this->prepareData($this->data);

        $columnFields = ['name', 'description', 'label'];

        $table = $this->describe->getTable($this->data['name']);

        foreach ($table as $column) {
            if ($column->isAutoIncrement()) {
                continue;
            }

            $field = strtolower($column->getField());
            $method = 'set_'.$field;

            $this->data['camel'][$field] = lcfirst(Inflector::camelize($method));
            $this->data['underscore'][$field] = Inflector::underscore($method);

            array_push($this->data['columns'], $field);

            if ($column->isForeignKey()) {
                $referencedTable = Tools::stripTableSchema(
                    $column->getReferencedTable()
                );

                $this->data['foreignKeys'][$field] = $referencedTable;

                array_push($this->data['models'], $referencedTable);

                $dropdown = [
                    'list' => plural($referencedTable),
                    'table' => $referencedTable,
                    'field' => $field
                ];

                if ( ! in_array($field, $columnFields)) {
                    $dropdown['field'] = $this->describe->getPrimaryKey(
                        $referencedTable
                    );
                }

                array_push($this->data['dropdowns'], $dropdown);
            }
        }

        return $this->data;
    }
}
