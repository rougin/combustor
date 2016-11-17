<?php

namespace Rougin\Combustor\Common;

use Symfony\Component\Console\Input\InputInterface;

use Rougin\Combustor\Exceptions\TableNotFoundException;

/**
 * Data Generator
 *
 * @package Combustor
 * @author  Rougin Royce Gutib <rougingutib@gmail.com>
 */
class DataGenerator
{
    /**
     * @var \Rougin\Describe\Describe
     */
    protected $describe;

    /**
     * @var array
     */
    protected $inputs = [];

    /**
     * @param \Rougin\Describe\Describe                       $describe
     * @param \Symfony\Component\Console\Input\InputInterface $input
     */
    public function __construct(\Rougin\Describe\Describe $describe, InputInterface $input)
    {
        $inputs = $input->getArguments();

        $inputs['columns']      = [];
        $inputs['foreign_keys'] = [];
        $inputs['primary_key']  = [];

        $this->describe = $describe;
        $this->inputs   = $inputs;
    }

    /**
     * Generates set of code based on given data.
     *
     * @return array
     */
    public function generate()
    {
        $columns = $this->getTableInformation($this->inputs['table']);
        $primary = $this->describe->getPrimaryKey($this->inputs['table']);

        foreach ($columns as $column) {
            if ($column->isForeignKey()) {
                array_push($this->inputs['foreign_keys'], $column->getField());
            }
        }

        $this->inputs['columns']     = $columns;
        $this->inputs['primary_key'] = $primary;

        return $this->inputs;
    }

    /**
     * Gets the table information from Describe.
     *
     * @param  string $tableName
     * @return array
     */
    protected function getTableInformation($tableName)
    {
        $tableInformation = $this->describe->getTable($tableName);

        if (empty($tableInformation)) {
            throw new TableNotFoundException('"' . $tableName . '" table not found in database!');
        }

        return $tableInformation;
    }
}
