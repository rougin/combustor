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
    const TYPE_WILDFIRE = 0;

    const TYPE_DOCTRINE = 1;

    /**
     * @var integer
     */
    protected $type;

    /**
     * @param string  $table
     * @param integer $type
     */
    public function __construct($table, $type)
    {
        $this->type = $type;

        $this->init($table);
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

        $type = $this->type;

        $this->addClassProperty('db', 'CI_DB_query_builder')->asTag();

        $this->addClassProperty('input', 'CI_Input')->asTag();

        if ($type === self::TYPE_DOCTRINE)
        {
            $this->imports[] = 'Rougin\Credo\Credo';

            $this->addClassProperty('load', 'MY_Loader')->asTag();
        }

        $this->addClassProperty('session', 'CI_Session')->asTag();

        $this->addClassProperty($model, $class)->asTag();

        if ($type === self::TYPE_DOCTRINE)
        {
            $repo = $model . '_repository';

            $class = ucfirst($repo);

            $this->addClassProperty($repo, $class)->asTag();

            $this->addClassProperty('repo', $class)->asPrivate();
        }

        $this->setName(ucfirst($name));

        $this->extendsTo('Rougin\SparkPlug\Controller');

        $this->setConstructor($model, $type);

        $this->setCreateMethod($name, $model, $type);

        $this->setDeleteMethod($name, $model, $type);

        $this->setEditMethod($name, $model, $type);

        $this->setIndexMethod($name, $model, $type);
    }

    /**
     * @param string  $model
     * @param integer $type
     *
     * @return void
     */
    protected function setConstructor($model, $type)
    {
        $method = new Method('__construct');

        $method->setComment('Loads the required helpers, libraries, and models.');

        $method->setCodeLine(function ($lines) use ($model, $type)
        {
            $lines[] = 'parent::__construct();';
            $lines[] = '';

            $lines[] = '// Initialize the Database loader ---';

            if ($type === self::TYPE_WILDFIRE)
            {
                $lines[] = '$this->load->helper(\'inflector\');';
            }

            $lines[] = '$this->load->database();';
            $lines[] = '// ----------------------------------';
            $lines[] = '';

            // TODO: Show if --with-view enabled -------------
            $lines[] = '// Load view-related helpers ------';
            $lines[] = '$this->load->helper(\'form\');';
            $lines[] = '$this->load->helper(\'url\');';
            $lines[] = '$this->load->library(\'pagination\');';
            $lines[] = '$this->load->helper(\'session\');';
            $lines[] = '// --------------------------------';
            $lines[] = '';
            // -----------------------------------------------

            $lines[] = '// Load multiple models if required ---';
            $lines[] = '$this->load->model(\'' . $model . '\');';

            if ($type === self::TYPE_DOCTRINE)
            {
                $lines[] = '';
                $lines[] = '$this->load->repository(\'' . $model . '\');';
            }

            $lines[] = '// ------------------------------------';

            $model = ucfirst($model);

            if ($type === self::TYPE_DOCTRINE)
            {
                $lines[] = '';
                $lines[] = '// Load the main repository of the model ---';
                $lines[] = '$credo = new Credo($this->db);';
                $lines[] = '';
                $lines[] = '/** @var \\' . $model . '_repository */';
                $lines[] = '$repo = $credo->get_repository(\'' . $model . '\');';
                $lines[] = '';
                $lines[] = '$this->repo = $repo;';
                $lines[] = '// -----------------------------------------';
            }

            return $lines;
        });

        $this->addMethod($method);
    }

    /**
     * @param string  $name
     * @param string  $model
     * @param integer $type
     *
     * @return void
     */
    protected function setCreateMethod($name, $model, $type)
    {
        $method = new Method('create');

        $texts = array('Returns the form page for creating a ' . $model . '.');
        $texts[] = 'Creates a new ' . $model . ' if receiving payload.';
        $method->setComment($texts);

        $method->setReturn('void');

        $method->setCodeLine(function ($lines) use ($name, $model, $type)
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

            if ($type === self::TYPE_WILDFIRE)
            {
                $lines[] = '$exists = $this->' . $model . '->exists($input);';
            }
            else
            {
                $lines[] = '$exists = $this->repo->exists($input);';
            }

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

            if ($type === self::TYPE_WILDFIRE)
            {
                $lines[] = '$this->' . $model . '->create($input);';
            }
            else
            {
                $class = ucfirst($model);

                $lines[] = '$this->repo->create($input, new ' . $class . ');';
            }

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
    }

    /**
     * @param string  $name
     * @param string  $model
     * @param integer $type
     *
     * @return void
     */
    protected function setDeleteMethod($name, $model, $type)
    {
        $method = new Method('delete');

        $method->setComment('Deletes the specified ' . $model . '.');

        $method->addIntegerArgument('id');

        $method->setReturn('void');

        $method->setCodeLine(function ($lines) use ($name, $model, $type)
        {
            $lines[] = '// Show 404 page if not using "DELETE" method ---';
            $lines[] = '$method = $this->input->post(\'_method\', true);';
            $lines[] = '';

            if ($type === self::TYPE_WILDFIRE)
            {
                $lines[] = '$item = $this->' . $model . '->find($id);';
            }
            else
            {
                $lines[] = '$item = $this->repo->find($id);';
            }

            $lines[] = '';
            $lines[] = 'if ($method !== \'DELETE\' || ! $item)';
            $lines[] = '{';
            $lines[] = '    show_404();';
            $lines[] = '}';
            $lines[] = '// ----------------------------------------------';
            $lines[] = '';

            $lines[] = '// Delete the item then go back to "index" page ---';

            if ($type === self::TYPE_WILDFIRE)
            {
                $lines[] = '$this->' . $model . '->delete($id);';
            }
            else
            {
                $class = ucfirst($model);

                $lines[] = '/** @var \\' . $class . ' $item */';
                $lines[] = '$this->repo->delete($item);';
            }

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
    }

    /**
     * @param string  $name
     * @param string  $model
     * @param integer $type
     *
     * @return void
     */
    protected function setEditMethod($name, $model, $type)
    {
        $method = new Method('edit');

        $texts = array('Returns the form page for updating a ' . $model . '.');
        $texts[] = 'Updates the specified ' . $model . ' if receiving payload.';
        $method->setComment($texts);

        $method->addIntegerArgument('id');

        $method->setReturn('void');

        $method->setCodeLine(function ($lines) use ($name, $model, $type)
        {
            $lines[] = '// Show 404 page if item not found ---';

            if ($type === self::TYPE_WILDFIRE)
            {
                $lines[] = 'if (! $item = $this->' . $model . '->find($id))';
            }
            else
            {
                $lines[] = 'if (! $item = $this->repo->find($id))';
            }

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

            if ($type === self::TYPE_WILDFIRE)
            {
                $lines[] = '$exists = $this->' . $model . '->exists($input, $id);';
            }
            else
            {
                $lines[] = '$exists = $this->repo->exists($input, $id);';
            }

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
            $lines[] = '    $this->load->view(\'' . $name . '/edit\', $data);';
            $lines[] = '';
            // --------------------------------------------------------------

            $lines[] = '    return;';
            $lines[] = '}';
            $lines[] = '// ------------------------------------';
            $lines[] = '';

            $lines[] = '// Update the item then go back to "index" page ---';

            if ($type === self::TYPE_WILDFIRE)
            {
                $lines[] = '$this->' . $model . '->update($id, $input);';
            }
            else
            {
                $class = ucfirst($model);

                $lines[] = '/** @var \\' . $class . ' $item */';
                $lines[] = '$this->repo->update($item, $input);';
            }

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
    }

    /**
     * @param string  $name
     * @param string  $model
     * @param integer $type
     *
     * @return void
     */
    protected function setIndexMethod($name, $model, $type)
    {
        $method = new Method('index');

        $method->setComment('Returns the list of paginated ' . $name . '.');

        $method->setReturn('void');

        $method->setCodeLine(function ($lines) use ($name, $model, $type)
        {
            $lines[] = '// Create pagination links and get the offset ---';

            if ($type === self::TYPE_WILDFIRE)
            {
                $lines[] = '$total = (int) $this->' . $model . '->total();';
            }
            else
            {
                $lines[] = '$total = (int) $this->repo->total();';
            }

            $lines[] = '';
            $lines[] = '$result = $this->' . $model . '->paginate(10, $total);';
            $lines[] = '';
            $lines[] = '$data = array(\'links\' => $result[1]);';
            $lines[] = '';
            $lines[] = '/** @var integer */';
            $lines[] = '$offset = $result[0];';
            $lines[] = '// ----------------------------------------------';
            $lines[] = '';

            if ($type === self::TYPE_WILDFIRE)
            {
                $lines[] = '$items = $this->' . $model . '->get(10, $offset);';
            }
            else
            {
                $lines[] = '$items = $this->repo->get(10, $offset);';
            }

            $lines[] = '';

            if ($type === self::TYPE_WILDFIRE)
            {
                $lines[] = '$data[\'items\'] = $items->result();';
            }
            else
            {
                $lines[] = '$data[\'items\'] = $items;';
            }

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
