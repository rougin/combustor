<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * {{ name | title | replace({'_': ' '}) }} Controller
 *
 * @package  CodeIgniter
 * @category Controller
 */
class {{ name | capitalize }} extends CI_Controller {

    public function __construct()
    {
        parent::__construct();

        $this->load->model(array(
{% for model in models %}
            '{{ model }}'{{ loop.index < loop.length ? ',' : '' }}
{% endfor %}
        ));
    }

    /**
     * Show the form for creating a new {{ singular | lower | replace({'_': ' '}) }}
     *
     * @return void
     */
    public function create()
    {
        $this->_set_form_validation();

        if ($this->form_validation->run())
        {
{% for column in columns if column != 'datetime_updated' %}
{% set hasGenders = (column.field == 'gender') ? TRUE : FALSE %}
{% set hasMaritalStatus = (column.field == 'marital_status') ? TRUE : FALSE %}
{% if column == 'datetime_created' %}
            $this->{{ singular }}->{{ isCamel ? camel[column] : underscore[column] }}('now');
{% else %}
{% if column in foreignKeys|keys %}

            ${{ foreignKeys[column] }} = $this->{{ type }}{{ type == 'doctrine' ? '->entity_manager' : ''}}->find('{{ foreignKeys[column] }}', $this->input->post('{{ column }}'));
            $this->{{ singular }}->{{ isCamel ? camel[column] : underscore[column] }}(${{ foreignKeys[column] }});{{ columns[loop.index0 + 1] in foreignKeys|keys ? '' : "\n"}}
{% else %}
            $this->{{ singular }}->{{ isCamel ? camel[column] : underscore[column] }}($this->input->post('{{ column }}'));
{% endif %}
{% endif %}
{% endfor %}

{% if type == 'doctrine' %}
            $this->doctrine->entity_manager->persist($this->{{ singular }});
            $this->doctrine->entity_manager->flush();
{% else %}
            $this->{{ singular }}->save();
{% endif %}

            $this->session->set_flashdata('notification', 'The {{ singular | lower | replace({'_': ' '}) }} has been created successfully!');
            $this->session->set_flashdata('alert', 'success');

            redirect('{{ plural }}');
        }
{% if dropdowns | length > 0 %}

{% for dropdown in dropdowns %}
        $data['{{ dropdown.list }}'] = $this->{{ type }}->get_all('{{ dropdown.table }}')->as_dropdown('{{ dropdown.field }}');
{% endfor %}
{% endif %}
{% if hasGenders %}
        $data['genders'] = array('male' => 'Male', 'female' => 'Female');
{% elseif hasMaritalStatus %}
        $data['marital_statuses'] = array(
            'single' => 'Single',
            'married' => 'Married',
            'widowed' => 'Widowed',
            'seperated' => 'Seperated',
            'divorced' => 'Divorced'
        );
{% endif %}

        $this->load->view('{{ plural }}/create', $data);
    }

    /**
     * Delete the specified {{ singular | lower | replace({'_': ' '}) }} from storage
     * 
     * @param  int $id
     * @return void
     */
    public function delete($id)
    {
        if ( ! isset($id))
        {
            show_404();
        }

{% if type == 'doctrine' %}
        ${{ singular }} = $this->doctrine->entity_manager->find('{{ singular }}', $id);

        $this->doctrine->entity_manager->remove(${{ singular }});
        $this->doctrine->entity_manager->flush();
{% else %}
        $this->wildfire->delete('{{ singular }}', $id);
{% endif %}

        $this->session->set_flashdata('notification', 'The {{ singular | lower | replace({'_': ' '}) }} has been deleted successfully!');
        $this->session->set_flashdata('alert', 'success');

        redirect('{{ plural }}');
    }

    /**
     * Show the form for editing the specified {{ singular | lower | replace({'_': ' '}) }}
     * 
     * @param  int $id
     * @return void
     */
    public function edit($id)
    {
        if ( ! isset($id))
        {
            show_404();
        }

        $this->_set_form_validation();

        if ($this->form_validation->run())
        {
            ${{ singular }} = $this->{{ type }}{{ type == 'doctrine' ? '->entity_manager' : ''}}->find('{{ singular }}', $id);

{% for column in columns if column != 'datetime_updated' %}
{% if column == 'datetime_created' %}
            ${{ singular }}->{{ isCamel ? camel[column] : underscore[column] }}('now');
{% else %}
{% if column in foreignKeys|keys %}

            ${{ foreignKeys[column] }} = $this->{{ type }}{{ type == 'doctrine' ? '->entity_manager' : ''}}->find('{{ foreignKeys[column] }}', $this->input->post('{{ column }}'));
            ${{ singular }}->{{ isCamel ? camel[column] : underscore[column] }}(${{ foreignKeys[column] }});{{ columns[loop.index0 + 1] in foreignKeys|keys ? '' : "\n"}}
{% else %}
            ${{ singular }}->{{ isCamel ? camel[column] : underscore[column] }}($this->input->post('{{ column }}'));
{% endif %}
{% endif %}
{% endfor %}

{% if type == 'doctrine' %}
            $this->doctrine->entity_manager->persist(${{ singular }});
            $this->doctrine->entity_manager->flush();
{% else %}
            ${{ singular }}->save();
{% endif %}

            $this->session->set_flashdata('notification', 'The {{ singular | lower | replace({'_': ' '}) }} has been updated successfully!');
            $this->session->set_flashdata('alert', 'success');

            redirect('{{ plural }}');
        }

        $data['{{ singular }}'] = $this->{{ type }}->find('{{ singular }}', $id);
{% for dropdown in dropdowns %}
        $data['{{ dropdown.list }}'] = $this->{{ type }}->get_all('{{ dropdown.table }}')->as_dropdown('{{ dropdown.field }}');
{% endfor %}
{% if hasGenders %}
        $data['genders'] = array('male' => 'Male', 'female' => 'Female');
{% elseif hasMaritalStatus %}
        $data['marital_statuses'] = array(
            'single' => 'Single',
            'married' => 'Married',
            'widowed' => 'Widowed',
            'seperated' => 'Seperated',
            'divorced' => 'Divorced'
        );
{% endif %}

        $this->load->view('{{ plural }}/edit', $data);
    }

    /**
     * Display a listing of {{ plural | lower | replace({'_': ' '}) }}
     *
     * @return void
     */
    public function index()
    {
        $this->load->library('pagination');

        include APPPATH . 'config/pagination.php';

        $delimiters = array();
        $delimiters['keyword'] = $this->input->get('keyword');

        $config['base_url'] = base_url('{{ plural }}');
        $config['suffix'] = '&keyword=' . $delimiters['keyword'];
        $config['total_rows'] = $this->{{ type }}->get_all('{{ singular }}', $delimiters)->total_rows();

        $delimiters['page'] = $this->input->get($config['query_string_segment']);
        $delimiters['per_page'] = $config['per_page'];

        $this->pagination->initialize($config);

        $data['{{ plural }}'] = $this->{{ type }}->get_all('{{ singular }}', $delimiters)->result();
        $data['links'] = $this->pagination->create_links();

        $this->load->view('{{ plural }}/index', $data);
    }

    /**
     * Display the specified {{ singular | lower | replace({'_': ' '}) }}
     * 
     * @param  int $id
     * @return void
     */
    public function show($id)
    {
        if ( ! isset($id))
        {
            show_404();
        }

        $data['{{ singular }}'] = $this->{{ type }}{{ type == 'doctrine' ? '->entity_manager' : ''}}->find('{{ singular }}', $id);

        $this->load->view('{{ plural }}/show', $data);
    }

    /**
     * Validate the input retrieved from the view
     * 
     * @return void
     */
    private function _set_form_validation()
    {
        $this->load->library('form_validation');

{% for column in columns %}
{% if column in foreignKeys|keys %}
        $this->form_validation->set_rules('{{ column }}', '{{ foreignKeys[column] | lower | capitalize | replace({'_': ' '}) }}', 'required|greater_than[0]');
{% elseif column != 'datetime_created' and column != 'datetime_updated' %}
        $this->form_validation->set_rules('{{ column }}', '{{ column | capitalize | replace({'_': ' '}) }}', 'required');
{% endif %}
{% endfor %}
    }

}