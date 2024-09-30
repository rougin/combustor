<?php

use Rougin\Credo\Model;
use Rougin\Credo\Traits\PaginateTrait;
use Rougin\Credo\Traits\ValidateTrait;

/**
 * @Entity(repositoryClass="User_repository")
 *
 * @Table(name="users")
 *
 */
class User extends Model
{
    use PaginateTrait;
    use ValidateTrait;

    /**
     * @Column(name="admin", type="boolean", nullable=false, unique=false)
     *
     * @var boolean
     */
    protected $admin;

    /**
     * @Column(name="created_at", type="datetime", nullable=false, unique=false)
     *
     * @var string
     */
    protected $created_at;

    /**
     * @Column(name="deleted_at", type="datetime", nullable=true, unique=false)
     *
     * @var string|null
     */
    protected $deleted_at = null;

    /**
     * @Column(name="email", type="string", length=100, nullable=false, unique=false)
     *
     * @var string
     */
    protected $email;

    /**
     * @Id @GeneratedValue
     *
     * @Column(name="id", type="integer", nullable=false, unique=false)
     *
     * @var integer
     */
    protected $id;

    /**
     * @Column(name="name", type="string", length=100, nullable=false, unique=false)
     *
     * @var string
     */
    protected $name;

    /**
     * @Column(name="remarks", type="string", length=100, nullable=true, unique=false)
     *
     * @var string|null
     */
    protected $remarks = null;

    /**
     * @Column(name="updated_at", type="datetime", nullable=true, unique=false)
     *
     * @var string|null
     */
    protected $updated_at = null;

    /**
     * @Column(name="year", type="integer", nullable=false, unique=false)
     *
     * @var integer
     */
    protected $year;

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
        array('field' => 'email', 'label' => 'Email', 'rules' => 'required'),
        array('field' => 'name', 'label' => 'Name', 'rules' => 'required'),
        array('field' => 'year', 'label' => 'Year', 'rules' => 'required'),
        array('field' => 'admin', 'label' => 'Admin', 'rules' => 'required'),
    );

    /**
     * @return boolean
     */
    public function is_admin()
    {
        return $this->admin;
    }

    /**
     * @return datetime
     */
    public function get_created_at()
    {
        return $this->created_at;
    }

    /**
     * @return datetime|null
     */
    public function get_deleted_at()
    {
        return $this->deleted_at;
    }

    /**
     * @return string
     */
    public function get_email()
    {
        return $this->email;
    }

    /**
     * @return integer
     */
    public function get_id()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function get_name()
    {
        return $this->name;
    }

    /**
     * @return string|null
     */
    public function get_remarks()
    {
        return $this->remarks;
    }

    /**
     * @return datetime|null
     */
    public function get_updated_at()
    {
        return $this->updated_at;
    }

    /**
     * @return integer
     */
    public function get_year()
    {
        return $this->year;
    }

    /**
     * @param boolean $admin
     *
     * @return self
     */
    public function set_admin($admin)
    {
        $this->admin = $admin;

        return $this;
    }

    /**
     * @param string $created_at
     *
     * @return self
     */
    public function set_created_at($created_at)
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * @param string|null $deleted_at
     *
     * @return self
     */
    public function set_deleted_at($deleted_at = null)
    {
        $this->deleted_at = $deleted_at;

        return $this;
    }

    /**
     * @param string $email
     *
     * @return self
     */
    public function set_email($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @param string $name
     *
     * @return self
     */
    public function set_name($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param string|null $remarks
     *
     * @return self
     */
    public function set_remarks($remarks = null)
    {
        $this->remarks = $remarks;

        return $this;
    }

    /**
     * @param string|null $updated_at
     *
     * @return self
     */
    public function set_updated_at($updated_at = null)
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     * @param integer $year
     *
     * @return self
     */
    public function set_year($year)
    {
        $this->year = $year;

        return $this;
    }
}
