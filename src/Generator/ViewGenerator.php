<?php

namespace Rougin\Combustor\Generator;

use Rougin\Combustor\Generator\GeneratorInterface;

class ViewGenerator implements GeneratorInterface
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
        if ($data['isBootstrap']) {
            $data['bootstrap'] = [
                'button' => 'btn btn-default',
                'buttonPrimary' => 'btn btn-primary',
                'formControl' => 'form-control',
                'formGroup' => 'form-group col-lg-12 col-md-12 ' .
                    'col-sm-12 col-xs-12',
                'label' => 'control-label',
                'table' => 'table table table-striped table-hover',
                'textRight' => 'text-right'
            ];
        }

        $data['camel'] = [];
        $data['underscore'] = [];
        $data['foreignKeys'] = [];
        $data['primaryKeys'] = [];

        $data['plural'] = plural($data['name']);
        $data['singular'] = singular($data['name']);

        $data['primaryKey'] = 'get_' . $this->describe->getPrimaryKey(
            $data['name']
        );

        if ($this->data['isCamel']) {
            $data['primaryKey'] = camelize($data['primaryKey']);
        }

        $data['columns'] = $this->describe->getTable(
            $data['name']
        );
    }

    public function generate()
    {
        $this->prepareData($this->data);

        foreach ($this->data['columns'] as $column) {
            $field = strtolower($column->getField());
            $accessor = 'get_' . $field;
            $mutator = 'set_' . $field;

            if ($this->data['isCamel']) {
                $this->data['camel'][$field] = array(
                    'field' => lcfirst(camelize($field)),
                    'accessor' => lcfirst(camelize($accessor)),
                    'mutator' => lcfirst(camelize($mutator))
                );
            } else {
                $this->data['underscore'][$field] = array(
                    'field' => lcfirst(underscore($field)),
                    'accessor' => lcfirst(underscore($accessor)),
                    'mutator' => lcfirst(underscore($mutator))
                );
            }

            if ($column->isForeignKey()) {
                $referencedTable = Tools::stripTableSchema(
                    $column->getReferencedTable()
                );

                $this->data['foreignKeys'][$field] = plural(
                    $referencedTable
                );

                $singular = $field . '_singular';

                $this->data['foreignKeys'][$singular] = singular(
                    $referencedTable
                );

                $this->data['primaryKeys'][$field] = 'get_' .
                    $this->describe->getPrimaryKey($referencedTable);

                if ($this->data['isCamel']) {
                    $this->data['primaryKeys'][$field] = camelize(
                        $this->data['primaryKeys'][$field]
                    );
                }
            }
        }

        return $this->data;
    }
}