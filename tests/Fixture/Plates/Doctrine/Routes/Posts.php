<?php

use Rougin\Credo\Credo;
use Rougin\SparkPlug\Controller;

/**
 * @property \CI_DB_query_builder $db
 * @property \CI_Input            $input
 * @property \MY_Loader           $load
 * @property \CI_Session          $session
 * @property \User                $user
 * @property \Post                $post
 */
class Posts extends Controller
{
    /**
     * @var \User_repository
     */
    private $user_repo;

    /**
     * @var \Post_repository
     */
    private $post_repo;

    /**
     * Loads the required helpers, libraries, and models.
     */
    public function __construct()
    {
        parent::__construct();

        // Initialize the Database loader ---
        $this->load->database();
        // ----------------------------------

        // Load view-related helpers ------
        $this->load->helper('form');
        $this->load->helper('url');
        $this->load->library('pagination');
        $this->load->library('session');
        // --------------------------------

        // Load multiple models if required ---
        $this->load->model('user');
        $this->load->model('post');

        $this->load->repository('user');
        $this->load->repository('post');
        // ------------------------------------

        // Load the required entity repositories ---
        $credo = new Credo($this->db);

        /** @var \User_repository */
        $repo = $credo->get_repository('User');
        $this->user_repo = $repo;

        /** @var \Post_repository */
        $repo = $credo->get_repository('Post');
        $this->post_repo = $repo;
        // -----------------------------------------
    }

    /**
     * Returns the form page for creating a post.
     * Creates a new post if receiving payload.
     *
     * @return void
     */
    public function create()
    {
        // Skip if provided empty input ---
        /** @var array<string, mixed> */
        $input = $this->input->post(null, true);

        $data = array();

        $data['users'] = $this->user_repo->dropdown('id');

        if (! $input)
        {
            $this->load->view('layout/header');
            $this->load->view('posts/create', $data);
            $this->load->view('layout/footer');

            return;
        }
        // --------------------------------

        // Specify logic here if applicable ---
        $exists = $this->post_repo->exists($input);

        if ($exists)
        {
            $data['error'] = '';
        }
        // ------------------------------------

        // Check if provided input is valid ---
        $valid = $this->post->validate($input);

        if (! $valid || $exists)
        {
            $this->load->view('layout/header');
            $this->load->view('posts/create', $data);
            $this->load->view('layout/footer');

            return;
        }
        // ------------------------------------

        // Create the item then go back to "index" page ---
        $this->post_repo->create($input, new Post);

        $this->post_repo->flush();

        $text = (string) 'Post successfully created!';

        $this->session->set_flashdata('alert', $text);

        redirect('posts');
        // ------------------------------------------------
    }

    /**
     * Deletes the specified post.
     *
     * @param integer $id
     *
     * @return void
     */
    public function delete($id)
    {
        // Show 404 page if not using "DELETE" method ---
        $method = $this->input->post('_method', true);

        $item = $this->post_repo->find($id);

        if ($method !== 'DELETE' || ! $item)
        {
            show_404();
        }
        // ----------------------------------------------

        // Delete the item then go back to "index" page ---
        /** @var \Post $item */
        $this->post_repo->delete($item);

        $this->post_repo->flush();

        $text = (string) 'Post successfully deleted!';

        $this->session->set_flashdata('alert', $text);

        redirect('posts');
        // ------------------------------------------------
    }

    /**
     * Returns the form page for updating a post.
     * Updates the specified post if receiving payload.
     *
     * @param integer $id
     *
     * @return void
     */
    public function edit($id)
    {
        // Show 404 page if item not found ---
        if (! $item = $this->post_repo->find($id))
        {
            show_404();
        }

        /** @var \Post $item */
        $data = array('item' => $item);
        // -----------------------------------

        $data['users'] = $this->user_repo->dropdown('id');

        // Skip if provided empty input ---
        /** @var array<string, mixed> */
        $input = $this->input->post(null, true);

        if (! $input)
        {
            $this->load->view('layout/header');
            $this->load->view('posts/edit', $data);
            $this->load->view('layout/footer');

            return;
        }
        // --------------------------------

        // Show 404 page if not using "PUT" method ---
        $method = $this->input->post('_method', true);

        if ($method !== 'PUT')
        {
            show_404();
        }
        // -------------------------------------------

        // Specify logic here if applicable ---
        $exists = $this->post_repo->exists($input, $id);

        if ($exists)
        {
            $data['error'] = '';
        }
        // ------------------------------------

        // Check if provided input is valid ---
        $valid = $this->post->validate($input);

        if (! $valid || $exists)
        {
            $this->load->view('layout/header');
            $this->load->view('posts/edit', $data);
            $this->load->view('layout/footer');

            return;
        }
        // ------------------------------------

        // Update the item then go back to "index" page ---
        /** @var \Post $item */
        $this->post_repo->update($item, $input);

        $this->post_repo->flush();

        $text = (string) 'User successfully updated!';

        $this->session->set_flashdata('alert', $text);

        redirect('posts');
        // ------------------------------------------------
    }

    /**
     * Returns the list of paginated posts.
     *
     * @return void
     */
    public function index()
    {
        // Create pagination links and get the offset ---
        $total = (int) $this->post_repo->total();

        $result = $this->post->paginate(10, $total);

        $data = array('links' => $result[1]);

        /** @var integer */
        $offset = $result[0];
        // ----------------------------------------------

        $items = $this->post_repo->get(10, $offset);

        $data['items'] = $items;

        if ($alert = $this->session->flashdata('alert'))
        {
            $data['alert'] = $alert;
        }

        $this->load->view('layout/header');
        $this->load->view('posts/index', $data);
        $this->load->view('layout/footer');
    }
}
