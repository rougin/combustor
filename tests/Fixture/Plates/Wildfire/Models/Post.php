<?php

use Rougin\Wildfire\Model;
use Rougin\Wildfire\Traits\PaginateTrait;
use Rougin\Wildfire\Traits\ValidateTrait;
use Rougin\Wildfire\Traits\WildfireTrait;
use Rougin\Wildfire\Traits\WritableTrait;

/**
 * @property integer|null $id
 * @property string       $title
 * @property string       $text
 * @property integer|null $user_id
 * @property string       $created_at
 * @property string|null  $updated_at
 * @property string|null  $deleted_at
 */
class Post extends Model
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
        array('field' => 'title', 'label' => 'Title', 'rules' => 'required'),
        array('field' => 'text', 'label' => 'Text', 'rules' => 'required'),
    );

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'posts';

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
        $title = $data['title'];
        $load['title'] = $title;

        /** @var string */
        $text = $data['text'];
        $load['text'] = $text;

        if (array_key_exists('user_id', $data))
        {
            /** @var integer|null */
            $user_id = $data['user_id'];
            $load['user_id'] = $user_id;
        }
        // -----------------------------------

        return $load;
    }
}
