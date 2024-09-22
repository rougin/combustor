<?php

namespace Rougin\Combustor\Template;

use Rougin\Classidy\Classidy;
use Rougin\Classidy\Method;
use Rougin\Combustor\Inflector;

/**
 * @package Combustor
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class Controller extends Classidy
{
    /**
     * @var boolean
     */
    protected $lower = false;

    /**
     * @param string  $table
     * @param boolean $lower
     */
    public function __construct($table, $lower = false)
    {
        $this->init($table);

        $this->lower = $lower;
    }

    /**
     * Configures the current class.
     *
     * @param string $table
     *
     * @return void
     */
    public function init($table)
    {
        $name = Inflector::plural($table);

        $model = Inflector::singular($table);

        /** @var class-string */
        $class = ucfirst($model);

        $ctrl = ucfirst($name);

        if ($this->lower)
        {
            /** @var class-string */
            $class = $model;

            $ctrl = $name;
        }

        $this->addClassProperty('db', 'CI_DB_query_builder')->asTag();
        $this->addClassProperty('input', 'CI_Input')->asTag();
        // $this->addClassProperty('load', 'MY_Loader')->asTag();
        $this->addClassProperty('session', 'CI_Session')->asTag();
        $this->addClassProperty($model, $class)->asTag();
        // $this->addClassProperty('user_repository', 'User_repository')->asTag();

        $this->setName($ctrl);

        $extends = 'Rougin\SparkPlug\Controller';
        $this->extendsTo($extends);

        $method = new Method('__construct');
        $method->setComment('Loads the required helpers, libraries, and models.');
        $method->setCodeLine(function ($lines) use ($model)
        {
            $lines[] = 'parent::__construct();';
            $lines[] = '';

            $lines[] = '// Initialize the Database loader ---';
            $lines[] = '$this->load->helper(\'inflector\');';
            $lines[] = '$this->load->database();';
            $lines[] = '// ----------------------------------';
            $lines[] = '';

            // TODO: Show if --with-view enabled -------------
            $lines[] = '// Load view-related helpers -------';
            $lines[] = '$this->load->helper(\'form\');';
            $lines[] = '$this->load->helper(\'url\');';
            $lines[] = '$this->load->helper(\'pagination\');';
            $lines[] = '$this->load->helper(\'session\');';
            $lines[] = '// ---------------------------------';
            $lines[] = '';
            // -----------------------------------------------

            $lines[] = '// Load multiple models if required ---';
            $lines[] = '$this->load->model(\'' . $model . '\');';
            $lines[] = '// ------------------------------------';

            return $lines;
        });
        $this->addMethod($method);

        $method = new Method('create');
        $texts = array('Returns the form page for creating a ' . $model . '.');
        $texts[] = 'Creates a new ' . $model . ' if receiving payload.';
        $method->setComment($texts);
        $method->setReturn('void');
        $method->setCodeLine(function ($lines) use ($name, $model)
        {
            $lines[] = '// Skip if provided empty input ---';
            $lines[] = '/** @var array<string, mixed> */';
            $lines[] = '$input = $this->input->post(null, true);';
            $lines[] = '';
            $lines[] = 'if (! $input)';
            $lines[] = '{';

            // TODO: Show if --with-view enabled -------------------------
            $lines[] = '    $this->load->view(\'' . $name . '/create\');';
            $lines[] = '';
            // -----------------------------------------------------------

            $lines[] = '    return;';
            $lines[] = '}';
            $lines[] = '// --------------------------------';
            $lines[] = '';

            $lines[] = '// Specify logic here if applicable ---';
            $lines[] = '$exists = $this->' . $model . '->exists($input);';
            $lines[] = '';
            $lines[] = '$data = array();';
            $lines[] = '';
            $lines[] = 'if ($exists)';
            $lines[] = '{';
            $lines[] = '    $data[\'error\'] = \'\';';
            $lines[] = '}';
            $lines[] = '// ------------------------------------';
            $lines[] = '';

            $lines[] = '// Check if provided input is valid ---';
            $lines[] = '$valid = $this->' . $model . '->validate($input);';
            $lines[] = '';
            $lines[] = 'if (! $valid || $exists)';
            $lines[] = '{';

            // TODO: Show if --with-view enabled ----------------------------
            $lines[] = '    $this->load->view(\'' . $name . '/create\', $data);';
            $lines[] = '';
            // --------------------------------------------------------------

            $lines[] = '    return;';
            $lines[] = '}';
            $lines[] = '// ------------------------------------';
            $lines[] = '';

            $lines[] = '// Create the item then go back to "index" page ---';
            $lines[] = '$this->' . $model . '->create($input);';
            $lines[] = '';
            $lines[] = '$text = (string) \'' . ucfirst($model) . ' successfully created!\';';
            $lines[] = '';
            $lines[] = '$this->session->set_flashdata(\'alert\', $text);';
            $lines[] = '';
            $lines[] = 'redirect(\'' . $name . '\');';
            $lines[] = '// ------------------------------------------------';

            return $lines;
        });
        $this->addMethod($method);

        $method = new Method('delete');
        $method->setComment('Deletes the specified ' . $model . '.');
        $method->addIntegerArgument('id');
        $method->setReturn('void');
        $method->setCodeLine(function ($lines) use ($name, $model)
        {
            $lines[] = '// Show 404 page if not using "DELETE" method ---';
            $lines[] = '$method = $this->input->post(\'_method\', true);';
            $lines[] = '';
            $lines[] = '$item = $this->' . $model . '->find($id);';
            $lines[] = '';
            $lines[] = 'if ($method !== \'DELETE\' || ! $item)';
            $lines[] = '{';
            $lines[] = '    show_404();';
            $lines[] = '}';
            $lines[] = '// ----------------------------------------------';
            $lines[] = '';

            $lines[] = '// Delete the item then go back to "index" page ---';
            $lines[] = '$this->' . $model . '->delete($id);';
            $lines[] = '';
            $lines[] = '$text = (string) \'' . ucfirst($model) . ' successfully deleted!\';';
            $lines[] = '';
            $lines[] = '$this->session->set_flashdata(\'alert\', $text);';
            $lines[] = '';
            $lines[] = 'redirect(\'' . $name . '\');';
            $lines[] = '// ------------------------------------------------';

            return $lines;
        });
        $this->addMethod($method);

        $method = new Method('edit');
        $texts = array('Returns the form page for updating a ' . $model . '.');
        $texts[] = 'Updates the specified ' . $model . ' if receiving payload.';
        $method->setComment($texts);
        $method->addIntegerArgument('id');
        $method->setReturn('void');
        $method->setCodeLine(function ($lines) use ($name, $model)
        {
            $lines[] = '// Show 404 page if item not found ---';
            $lines[] = 'if (! $item = $this->' . $model . '->find($id))';
            $lines[] = '{';
            $lines[] = '    show_404();';
            $lines[] = '}';
            $lines[] = '';
            $lines[] = '/** @var \\' . ucfirst($model) . ' $item */';
            $lines[] = '$data = array(\'item\' => $item);';
            $lines[] = '// -----------------------------------';
            $lines[] = '';

            $lines[] = '// Skip if provided empty input ---';
            $lines[] = '/** @var array<string, mixed> */';
            $lines[] = '$input = $this->input->post(null, true);';
            $lines[] = '';
            $lines[] = 'if (! $input)';
            $lines[] = '{';

            // TODO: Show if --with-view enabled ----------------------------
            $lines[] = '    $this->load->view(\'' . $name . '/edit\', $data);';
            $lines[] = '';
            // --------------------------------------------------------------

            $lines[] = '    return;';
            $lines[] = '}';
            $lines[] = '// --------------------------------';
            $lines[] = '';

            $lines[] = '// Show 404 page if not using "PUT" method ---';
            $lines[] = '$method = $this->input->post(\'_method\', true);';
            $lines[] = '';
            $lines[] = 'if ($method !== \'PUT\')';
            $lines[] = '{';
            $lines[] = '    show_404();';
            $lines[] = '}';
            $lines[] = '// -------------------------------------------';
            $lines[] = '';

            $lines[] = '// Specify logic here if applicable ---';
            $lines[] = '$exists = $this->' . $model . '->exists($input, $id);';
            $lines[] = '';
            $lines[] = 'if ($exists)';
            $lines[] = '{';
            $lines[] = '    $data[\'error\'] = \'Email already exists.\';';
            $lines[] = '}';
            $lines[] = '// ------------------------------------';
            $lines[] = '';

            $lines[] = '// Check if provided input is valid ---';
            $lines[] = '$valid = $this->' . $model . '->validate($input);';
            $lines[] = '';
            $lines[] = 'if (! $valid || $exists)';
            $lines[] = '{';

            // TODO: Show if --with-view enabled ----------------------------
            $lines[] = '    $this->load->view(\'' . $name . '/edit\', $data);';
            $lines[] = '';
            // --------------------------------------------------------------

            $lines[] = '    return;';
            $lines[] = '}';
            $lines[] = '// ------------------------------------';
            $lines[] = '';

            $lines[] = '// Update the item then go back to "index" page ---';
            $lines[] = '$this->' . $model . '->update($id, $input);';
            $lines[] = '';
            $lines[] = '$text = (string) \'User successfully updated!\';';
            $lines[] = '';
            $lines[] = '$this->session->set_flashdata(\'alert\', $text);';
            $lines[] = '';
            $lines[] = 'redirect(\'' . $name . '\');';
            $lines[] = '// ------------------------------------------------';

            return $lines;
        });
        $this->addMethod($method);

        $method = new Method('index');
        $method->setComment('Returns the list of paginated ' . $name . '.');
        $method->setReturn('void');
        $method->setCodeLine(function ($lines) use ($name, $model)
        {
            $lines[] = '// Create pagination links and get the offset ---';
            $lines[] = '$total = (int) $this->' . $model . '->total();';
            $lines[] = '';
            $lines[] = '$result = $this->' . $model . '->paginate(10, $total);';
            $lines[] = '';
            $lines[] = '$data = array(\'links\' => $result[1]);';
            $lines[] = '';
            $lines[] = '/** @var integer */';
            $lines[] = '$offset = $result[0];';
            $lines[] = '// ----------------------------------------------';
            $lines[] = '';

            $lines[] = '$items = $this->' . $model . '->get(10, $offset);';
            $lines[] = '';
            $lines[] = '$data[\'items\'] = $items->result();';
            $lines[] = '';
            $lines[] = 'if ($alert = $this->session->flashdata(\'alert\'))';
            $lines[] = '{';
            $lines[] = '    $data[\'alert\'] = $alert;';
            $lines[] = '}';

            // TODO: Show if --with-view enabled ---------------------------
            $lines[] = '';
            $lines[] = '$this->load->view(\'' . $name . '/index\', $data);';
            // -------------------------------------------------------------

            return $lines;
        });
        $this->addMethod($method);
    }
}
