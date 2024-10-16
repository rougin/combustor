<?php

use Rougin\Credo\Model;
use Rougin\Credo\Traits\PaginateTrait;
use Rougin\Credo\Traits\ValidateTrait;

/**
 * @Entity(repositoryClass="Post_repository")
 *
 * @Table(name="posts")
 */
class Post extends Model
{
    use PaginateTrait;
    use ValidateTrait;

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
     * @Id @GeneratedValue
     *
     * @Column(name="id", type="integer", nullable=true, unique=false)
     *
     * @var integer|null
     */
    protected $id = null;

    /**
     * @Column(name="text", type="string", nullable=false, unique=false)
     *
     * @var string
     */
    protected $text;

    /**
     * @Column(name="title", type="string", nullable=false, unique=false)
     *
     * @var string
     */
    protected $title;

    /**
     * @Column(name="updated_at", type="datetime", nullable=true, unique=false)
     *
     * @var string|null
     */
    protected $updated_at = null;

    /**
     * @ManyToOne(targetEntity="User", cascade={"persist"})
     * @JoinColumn(name="user_id", referencedColumnName="id", nullable=true, unique=false)
     *
     * @var \User|null
     */
    protected $user = null;

    /**
     * @Column(name="user_id", type="integer", nullable=true, unique=false)
     *
     * @var integer|null
     */
    protected $user_id = null;

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
        array('field' => 'title', 'label' => 'Title', 'rules' => 'required'),
        array('field' => 'text', 'label' => 'Text', 'rules' => 'required'),
    );

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
     * @return integer|null
     */
    public function get_id()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function get_text()
    {
        return $this->text;
    }

    /**
     * @return string
     */
    public function get_title()
    {
        return $this->title;
    }

    /**
     * @return datetime|null
     */
    public function get_updated_at()
    {
        return $this->updated_at;
    }

    /**
     * @return \User|null
     */
    public function get_user()
    {
        return $this->user;
    }

    /**
     * @return integer|null
     */
    public function get_user_id()
    {
        return $this->user_id;
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
     * @param string $text
     *
     * @return self
     */
    public function set_text($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @param string $title
     *
     * @return self
     */
    public function set_title($title)
    {
        $this->title = $title;

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
     * @param \User|null $user
     *
     * @return self
     */
    public function set_user(\User $user = null)
    {
        $this->user = $user;

        return $this;
    }
}
