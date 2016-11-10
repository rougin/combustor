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
        $this->describe = $describe;

        $inputs = $input->getArguments();

        $inputs['columns']     = [];
        $inputs['foreign']     = [];
        $inputs['plural']      = strtolower(plural($inputs['table']));
        $inputs['primary_key'] = $describe->getPrimaryKey($inputs['table']);
        $inputs['singular']    = strtolower(singular($inputs['table']));

        $this->inputs = $inputs;
    }

    /**
     * Generates set of code based on given data.
     *
     * @return array
     */
    public function generate()
    {
        $tableInformation = $this->getTableInformation($this->inputs['table']);

        foreach ($tableInformation as $column) {
            if ($column->isForeignKey()) {
                $table = $this->stripTableSchema($column->getReferencedTable());

                $column->setReferencedTable(singular($table));

                array_push($this->inputs['foreign'], [
                    'field'        => $column->getField(),
                    'field_plural' => plural($column->getField()),
                    'plural'       => plural($column->getReferencedTable()),
                    'singular'     => singular($column->getReferencedTable()),
                ]);
            }
        }

        $this->inputs['columns'] = $tableInformation;

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

    /**
     * Strips the table schema from the table name.
     *
     * @param  string $table
     * @return string
     */
    protected static function stripTableSchema($table)
    {
        return (strpos($table, '.') !== false) ? substr($table, strpos($table, '.') + 1) : $table;
    }
}
