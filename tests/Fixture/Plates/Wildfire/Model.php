<?php

use Rougin\Wildfire\Model;
use Rougin\Wildfire\Traits\PaginateTrait;
use Rougin\Wildfire\Traits\ValidateTrait;
use Rougin\Wildfire\Traits\WildfireTrait;
use Rougin\Wildfire\Traits\WritableTrait;

/**
 * @property integer              $id
 * @property string               $name
 * @property integer              $age
 * @property string               $gender
 * @property \CI_DB_query_builder $db
 */
class User extends Model
{
    use PaginateTrait;
    use ValidateTrait;
    use WildfireTrait;
    use WritableTrait;

    /**
     * Additional configuration to Pagination Class.
     *
     * @link https://codeigniter.com/userguide3/libraries/pagination.html#customizing-the-pagination
     *
     * @var array<string, mixed>
     */
    protected $pagee = array(
        'page_query_string' => true,
        'use_page_numbers' => true,
        'query_string_segment' => 'p',
        'reuse_query_string' => true,
    );

    /**
     * List of validation rules for Form Validation.
     *
     * @link https://codeigniter.com/userguide3/libraries/form_validation.html#setting-rules-using-an-array
     *
     * @var array<string, string>[]
     */
    protected $rules = array(
        array('field' => 'name', 'label' => 'Name', 'rules' => 'required'),
        array('field' => 'age', 'label' => 'Age', 'rules' => 'required'),
        array('field' => 'gender', 'label' => 'Gender', 'rules' => 'required'),
    );

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * @param array<string, mixed> $data
     * @param integer|null         $id
     *
     * @return boolean
     */
    public function exists($data, $id = null)
    {
        // Specify logic here if applicable ---
        // ------------------------------------

        return false;
    }

    /**
     * @param array<string, mixed> $data
     * @param integer|null         $id
     *
     * @return array<string, mixed>
     */
    protected function input($data, $id = null)
    {
        $load = array();

        // List editable fields from table ---
        /** @var string */
        $name = $data['name'];
        $load['name'] = $name;

        /** @var integer */
        $age = $data['age'];
        $load['age'] = $age;

        /** @var string */
        $gender = $data['gender'];
        $load['gender'] = $gender;
        // -----------------------------------

        return $load;
    }
}