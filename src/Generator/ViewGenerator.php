<?php

namespace Rougin\Combustor\Generator;

use Rougin\Describe\Describe;
use Rougin\Combustor\Common\Tools;
use Rougin\Combustor\Common\Inflector;

/**
 * View Generator
 *
 * Generates CodeIgniter-based views.
 * 
 * @package Combustor
 * @author  Rougin Royce Gutib <rougingutib@gmail.com>
 */
class ViewGenerator implements GeneratorInterface
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
     * Prepares the data before generation.
     * 
     * @param  array $data
     * @return void
     */
    public function prepareData(array &$data)
    {
        $bootstrap = [
            'button' => 'btn btn-default',
            'buttonPrimary' => 'btn btn-primary',
            'formControl' => 'form-control',
            'formGroup' => 'form-group col-lg-12 col-md-12 col-sm-12 col-xs-12',
            'label' => 'control-label',
            'table' => 'table table table-striped table-hover',
            'textRight' => 'text-right'
        ];

        if ($data['isBootstrap']) {
            $data['bootstrap'] = $bootstrap;
        }

        $data['camel'] = [];
        $data['underscore'] = [];
        $data['foreignKeys'] = [];
        $data['primaryKeys'] = [];

        $data['plural'] = Inflector::plural($data['name']);
        $data['singular'] = Inflector::singular($data['name']);

        $data['primaryKey'] = 'get_'.$this->describe->getPrimaryKey(
            $data['name']
        );

        // Workaround...
        if ($data['primaryKey'] == 'get_') {
            $data['primaryKey'] = 'get_'.$this->describe->getPrimaryKey(
                Inflector::singular($data['name'])
            );
        }

        if ($this->data['isCamel']) {
            $data['primaryKey'] = Inflector::camelize($data['primaryKey']);
        }

        $data['columns'] = $this->describe->getTable(
            $data['name']
        );

        // Workaround...
        if (empty($data['columns'])) {
            $data['columns'] = $this->describe->getTable(
                Inflector::singular($data['name'])
            );
        }
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
            $accessor = 'get_'.$field;
            $mutator = 'set_'.$field;

            $this->data['camel'][$field] = [
                'field' => lcfirst(Inflector::camelize($field)),
                'accessor' => lcfirst(Inflector::camelize($accessor)),
                'mutator' => lcfirst(Inflector::camelize($mutator))
            ];

            $this->data['underscore'][$field] = [
                'field' => lcfirst(Inflector::underscore($field)),
                'accessor' => lcfirst(Inflector::underscore($accessor)),
                'mutator' => lcfirst(Inflector::underscore($mutator))
            ];

            if ($column->isForeignKey()) {
                $referencedTable = Tools::stripTableSchema(
                    $column->getReferencedTable()
                );

                $this->data['foreignKeys'][$field] = Inflector::plural(
                    $referencedTable
                );

                $singular = $field.'_singular';

                $this->data['foreignKeys'][$singular] = Inflector::singular(
                    $referencedTable
                );

                $this->data['primaryKeys'][$field] = 'get_'.
                    $this->describe->getPrimaryKey($referencedTable);

                if ($this->data['isCamel']) {
                    $this->data['primaryKeys'][$field] = Inflector::camelize(
                        $this->data['primaryKeys'][$field]
                    );
                }
            }
        }

        return $this->data;
    }
}
