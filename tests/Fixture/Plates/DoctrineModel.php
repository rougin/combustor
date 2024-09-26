<?php

use Rougin\Credo\Model;
use Rougin\Credo\Traits\PaginateTrait;
use Rougin\Credo\Traits\ValidateTrait;

/**
 * @Entity(repositoryClass="User_repository")
 *
 * @Table(name="users")
 *
 * @property \CI_DB_query_builder $db
 */
class User extends Model
{
    use PaginateTrait;
    use ValidateTrait;

    /**
     * @Id @GeneratedValue
     *
     * @Column(name="id", type="integer", nullable=false, unique=false)
     *
     * @var integer
     */
    protected $id;

    /**
     * @Column(name="name", type="string", nullable=false, unique=false)
     *
     * @var string
     */
    protected $name;

    /**
     * @Column(name="age", type="integer", nullable=false, unique=false)
     *
     * @var integer
     */
    protected $age;

    /**
     * @Column(name="gender", type="string", nullable=false, unique=false)
     *
     * @var string
     */
    protected $gender;

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
}
