<?php

use Rougin\Credo\Credo;
use Rougin\SparkPlug\Controller;

/**
 * @property \CI_DB_query_builder $db
 * @property \CI_Input            $input
 * @property \MY_Loader           $load
 * @property \CI_Session          $session
 * @property \User                $user
 */
class Users extends Controller
{
    /**
     * @var \User_repository
     */
    private $user_repo;

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

        $this->load->repository('user');
        // ------------------------------------

        // Load the required entity repositories ---
        $credo = new Credo($this->db);

        /** @var \User_repository */
        $repo = $credo->get_repository('User');
        $this->user_repo = $repo;
        // -----------------------------------------
    }

    /**
     * Returns the form page for creating a user.
     * Creates a new user if receiving payload.
     *
     * @return void
     */
    public function create()
    {
        // Skip if provided empty input ---
        /** @var array<string, mixed> */
        $input = $this->input->post(null, true);

        $data = array();

        if (! $input)
        {
            $this->load->view('layout/header');
            $this->load->view('users/create', $data);
            $this->load->view('layout/footer');

            return;
        }
        // --------------------------------

        // Specify logic here if applicable ---
        $exists = $this->user_repo->exists($input);

        if ($exists)
        {
            $data['error'] = '';
        }
        // ------------------------------------

        // Check if provided input is valid ---
        $valid = $this->user->validate($input);

        if (! $valid || $exists)
        {
            $this->load->view('layout/header');
            $this->load->view('users/create', $data);
            $this->load->view('layout/footer');

            return;
        }
        // ------------------------------------

        // Create the item then go back to "index" page ---
        $this->user_repo->create($input, new User);

        $this->user_repo->flush();

        $text = (string) 'User successfully created!';

        $this->session->set_flashdata('alert', $text);

        redirect('users');
        // ------------------------------------------------
    }

    /**
     * Deletes the specified user.
     *
     * @param integer $id
     *
     * @return void
     */
    public function delete($id)
    {
        // Show 404 page if not using "DELETE" method ---
        $method = $this->input->post('_method', true);

        $item = $this->user_repo->find($id);

        if ($method !== 'DELETE' || ! $item)
        {
            show_404();
        }
        // ----------------------------------------------

        // Delete the item then go back to "index" page ---
        /** @var \User $item */
        $this->user_repo->delete($item);

        $this->user_repo->flush();

        $text = (string) 'User successfully deleted!';

        $this->session->set_flashdata('alert', $text);

        redirect('users');
        // ------------------------------------------------
    }

    /**
     * Returns the form page for updating a user.
     * Updates the specified user if receiving payload.
     *
     * @param integer $id
     *
     * @return void
     */
    public function edit($id)
    {
        // Show 404 page if item not found ---
        if (! $item = $this->user_repo->find($id))
        {
            show_404();
        }

        /** @var \User $item */
        $data = array('item' => $item);
        // -----------------------------------

        // Skip if provided empty input ---
        /** @var array<string, mixed> */
        $input = $this->input->post(null, true);

        if (! $input)
        {
            $this->load->view('layout/header');
            $this->load->view('users/edit', $data);
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
        $exists = $this->user_repo->exists($input, $id);

        if ($exists)
        {
            $data['error'] = '';
        }
        // ------------------------------------

        // Check if provided input is valid ---
        $valid = $this->user->validate($input);

        if (! $valid || $exists)
        {
            $this->load->view('layout/header');
            $this->load->view('users/edit', $data);
            $this->load->view('layout/footer');

            return;
        }
        // ------------------------------------

        // Update the item then go back to "index" page ---
        /** @var \User $item */
        $this->user_repo->update($item, $input);

        $this->user_repo->flush();

        $text = (string) 'User successfully updated!';

        $this->session->set_flashdata('alert', $text);

        redirect('users');
        // ------------------------------------------------
    }

    /**
     * Returns the list of paginated users.
     *
     * @return void
     */
    public function index()
    {
        // Create pagination links and get the offset ---
        $total = (int) $this->user_repo->total();

        $result = $this->user->paginate(10, $total);

        $data = array('links' => $result[1]);

        /** @var integer */
        $offset = $result[0];
        // ----------------------------------------------

        $items = $this->user_repo->get(10, $offset);

        $data['items'] = $items;

        if ($alert = $this->session->flashdata('alert'))
        {
            $data['alert'] = $alert;
        }

        $this->load->view('layout/header');
        $this->load->view('users/index', $data);
        $this->load->view('layout/footer');
    }
}
