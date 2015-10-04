<?php

namespace Rougin\Combustor\Generator;

use Rougin\Combustor\Generator\GeneratorInterface;
use Rougin\Describe\Describe;

class ControllerGenerator implements GeneratorInterface
{
    protected $describe;
    protected $data;

    public function __construct(Describe $describe, array $data)
    {
        $this->describe = $describe;
        $this->data = $data;
    }

    public function prepareData(array &$data)
    {
        $data['camel'] = [];
        $data['columns'] = [];
        $data['dropdowns'] = [];
        $data['foreignKeys'] = [];
        $data['models'] = [$data['name']];
        $data['name'] = $data['name'];
        $data['plural'] = plural($data['name']);
        $data['singular'] = singular($data['name']);
        $data['underscore'] = [];
    }

    public function generate()
    {
        $this->prepareData($this->data);

        $columnFields = ['name', 'description', 'label'];

        $table = $this->describe->getTable($data['name']);

        foreach ($table as $column) {
            if ($column->isAutoIncrement()) {
                continue;
            }

            $field = strtolower($column->getField());
            $method = 'set_'.$field;

            $this->data['camel'][$field] = lcfirst(camelize($method));
            $this->data['underscore'][$field] = underscore($method);

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
                    $dropdown['field'] = $describe->getPrimaryKey(
                        $referencedTable
                    );
                }

                array_push($this->data['dropdowns'], $dropdown);
            }
        }

        return $this->data;
    }
}