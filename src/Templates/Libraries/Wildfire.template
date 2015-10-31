<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Rougin\Describe\Describe;
use Rougin\Combustor\Tools;

/**
 * Wildfire Class
 *
 * @package  CodeIgniter
 * @category Library
 */
class Wildfire {

    private $_codeigniter;
    private $_describe;
    private $_query;
    private $_rows = array();
    private $_table;
    private $_tables = array();
    private $_joined_tables = array();

    /**
     * Loads the database and initialize Describe
     */
    public function __construct()
    {
        $this->_codeigniter =& get_instance();
        $this->_codeigniter->load->database();
        $this->_codeigniter->load->helper('inflector');

        require APPPATH . 'config/database.php';

        $db['default']['driver'] = $db['default']['dbdriver'];
        unset($db['default']['dbdriver']);

        $this->_describe = new Describe($db['default']);
    }

    /**
     * List all data in dropdown format
     *
     * @param  string $description
     * @return array
     */
    public function as_dropdown($description = 'description')
    {
        $table_information = $this->_describe->get_information_from_table($this->_table);

        $data        = array();
        $description = 'get_' . $description;
        $id          = 'get_' . $this->_describe->get_primary_key($this->_table);

        $result = $this->result();

        foreach ($result as $row) {
            $data[$row->$id()] = ucwords($row->$description());
        }

        return $data;
    }

    /**
     * Delete the specified data from storage
     * 
     * @param  string        $table
     * @param  array|integer $delimiters
     * @return boolean
     */
    public function delete($table, $delimiters = array())
    {
        if ( ! is_array($delimiters))
        {
            $id = $delimiters;
            $primary_key = $this->_describe->get_primary_key($table);

            $delimiters = array($primary_key => $id);
        }

        $this->_codeigniter->db->where($delimiters);
        return ($this->_codeigniter->db->delete($table)) ? TRUE : FALSE;
    }

    /**
     * Find the row from the specified ID or with the list of delimiters from the specified table
     *
     * @param  string           $table
     * @param  array|integer  delimiters
     * @return object|boolean
     */
    public function find($table, $delimiters = array())
    {
        if ( ! is_array($delimiters))
        {
            $id = $delimiters;
            $primary_key = $this->_describe->get_primary_key($table);

            $delimiters = array($primary_key => $id);
        }

        $this->_codeigniter->db->where($delimiters);
        $query = $this->_codeigniter->db->get($table);

        if ($query->num_rows() > 0)
        {
            return $this->_create_object($table, $query->row());
        }

        return FALSE;
    }

    /**
     * Return all rows from the specified table
     *
     * @param  array $delimiters
     * @return object|boolean
     */
    public function get_all($table, $delimiters = array())
    {
        $this->_rows = array();
        $this->_table = $table;

        if (isset($delimiters['keyword']) && $delimiters['keyword'] != NULL)
        {
            $this->_find_by_keyword($delimiters['keyword']);
        }

        if (isset($delimiters['per_page']) && $delimiters['per_page'] != NULL)
        {
            $page = $delimiters['page'];

            if ($page != NULL)
            {
                $page = $delimiters['per_page'] * $page - $delimiters['per_page'];
            }

            $this->_codeigniter->db->limit($delimiters['per_page'], $page);
        }

        $this->_query = $this->_codeigniter->db->get($table);

        return $this;
    }

    /**
     * Join a specified model into the result
     * 
     * @param  string $table
     * @param  string $referenced_field
     * @param  string $column
     * @return object
     */
    public function join($table, $referenced_field, $column)
    {
        $this->_joined_tables[$column] = array(
            'table' => $table,
            'referenced_field' => $referenced_field
        );

        return $this;
    }

    /**
     * Return the result
     * 
     * @return object
     */
    public function result()
    {
        foreach ($this->_query->result() as $row)
        {
            $this->_rows[] = $this->_create_object($this->_table, $row);
        }

        return $this->_rows;
    }

    /**
     * Return the number of rows from the result
     * 
     * @return int
     */
    public function total_rows()
    {
        return $this->_query->num_rows();
    }

    /**
     * Create an object from the specified data
     *
     * @param  string $table
     * @param  object $row
     * @return array
     */
    protected function _create_object($table, $row)
    {
        $new_table = singular(Tools::strip_table_schema($table));
        $model = new $new_table();

        if ( ! array_key_exists($table, $this->_tables))
        {
            $main_table_information = $this->_describe->get_information_from_table($table);
            $this->_tables[$table] = $main_table_information;
        }
        else
        {
            $main_table_information = $this->_tables[$table];
        }

        foreach ($row as $column => $value)
        {
            $mutator = 'set_' . $column;

            foreach ($main_table_information as $table_column)
            {
                if ($table_column->get_field() != $column)
                {
                    continue;
                }

                $data = $value;

                if ($table_column->is_foreign_key())
                {
                    $delimiters = array($table_column->get_referenced_field() => $data);
                    $data = $this->find($table_column->get_referenced_table(), $delimiters);
                }

                if ($data)
                {
                    $model->$mutator($data);
                }
            }

            if (isset($this->_joined_tables[$column]))
            {
                $delimiters = array($this->_joined_tables[$column]['referenced_field'] => $value);
                $data = $this->find($this->_joined_tables[$column]['table'], $delimiters);

                $model->$mutator($data);
            }
        }

        foreach ($main_table_information as $table_column)
        {
            $method  = $table_column->get_field();
            $mutator = 'set_' . $table_column->get_field();

            $data = $row->$method;

            if ($table_column->is_foreign_key())
            {
                $delimiters = array($table_column->get_referenced_field() => $data);
                $data = $this->find($table_column->get_referenced_table(), $delimiters);
            }

            if ($data)
            {
                $model->$mutator($data);
            }
        }

        return $model;
    }

    /**
     * Search for keywords based on the list of columns in the storage
     * 
     * @param  string $keyword
     * @param  string $table
     * @param  array  $tables
     * @param  string $table_alias
     */
    protected function _find_by_keyword($keyword, $table = NULL, $tables = array(), $table_alias = NULL)
    {
        if ($table == NULL)
        {
            $table = $this->_table;
        }

        if ($table_alias == NULL)
        {
            $table_alias = $table;
        }

        if ( ! array_key_exists($table, $this->_tables))
        {
            $table_information = $this->_describe->get_information_from_table($table);
            $this->_tables[$table] = $table_information;
        }
        else
        {
            $table_information = $this->_tables[$table];
        }

        array_push($tables, $table);

        foreach ($table_information as $column)
        {
            if ($column->is_foreign_key())
            {
                if ( ! in_array($column->get_referenced_table(), $tables))
                {
                    $foreign_primary_key = $this->_describe->get_primary_key($column->get_referenced_table());
                    $foreign_table_alias = rand(0, 100) . '_' . Tools::strip_table_schema($table) . '_' . Tools::strip_table_schema($column->get_referenced_table());
                    $condition = $foreign_table_alias . '.' . $foreign_primary_key . ' = ' . $table_alias . '.' . $column->get_field();

                    $this->_codeigniter->db->join($column->get_referenced_table() . ' as ' . $foreign_table_alias, $condition, 'left');

                    array_push($tables, $column->get_referenced_table());
                    $tables = array_unique($tables);
                    $this->_find_by_keyword($keyword, $column->get_referenced_table(), $tables, $foreign_table_alias);
                }
            }
            else if ( ! $column->is_primary_key())
            {
                $this->_codeigniter->db->or_like($table_alias . '.' . $column->get_field(), $keyword);
            }
        }
    }

}