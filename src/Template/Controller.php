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
     * @var \Rougin\Describe\Column[]
     */
    protected $cols;

    /**
     * @var boolean
     */
    protected $layout = false;

    /**
     * @var string
     */
    protected $table;

    /**
     * @var integer
     */
    protected $type;

    /**
     * @param string                    $table
     * @param \Rougin\Describe\Column[] $cols
     */
    public function __construct($table, $cols)
    {
        $this->cols = $cols;

        $this->table = $table;
    }

    /**
     * Configures the current class.
     *
     * @return self
     */
    public function init()
    {
        $name = Inflector::plural($this->table);

        $model = Inflector::singular($this->table);

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

        $models = $this->getForeignModels();
        $models[] = $model;

        foreach ($models as $item)
        {
            $this->addClassProperty($item, ucfirst($item))->asTag();
        }

        if ($type === self::TYPE_DOCTRINE)
        {
            foreach ($models as $item)
            {
                $repo = $item . '_repository';

                $repoName = strtolower($item . '_repo');

                $class = ucfirst($repo);

                $this->addClassProperty($repoName, $class)->asPrivate();
            }
        }

        $this->setName(ucfirst($name));

        $this->extendsTo('Rougin\SparkPlug\Controller');

        $this->setConstructor($model, $type);

        $this->setCreateMethod($name, $model, $type);

        $this->setDeleteMethod($name, $model, $type);

        $this->setEditMethod($name, $model, $type);

        $this->setIndexMethod($name, $model, $type);

        return $this;
    }

    /**
     * @param integer $type
     *
     * @return self
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @param boolean $layout
     *
     * @return self
     */
    public function useLayout($layout = true)
    {
        $this->layout = $layout;

        return $this;
    }

    /**
     * @return string[]
     */
    protected function getForeignModels()
    {
        $items = array();

        foreach ($this->cols as $col)
        {
            if (! $col->isForeignKey())
            {
                continue;
            }

            $name = $col->getReferencedTable();
            $name = Inflector::singular($name);

            $items[] = $name;
        }

        return $items;
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
            $lines[] = '$this->load->library(\'session\');';
            $lines[] = '// --------------------------------';
            $lines[] = '';
            // -----------------------------------------------

            $lines[] = '// Load multiple models if required ---';

            foreach ($this->getForeignModels() as $foreign)
            {
                $lines[] = '$this->load->model(\'' . $foreign . '\');';
            }

            $lines[] = '$this->load->model(\'' . $model . '\');';

            $foreigns = $this->getForeignModels();

            if ($type === self::TYPE_DOCTRINE)
            {
                $lines[] = '';

                foreach ($foreigns as $foreign)
                {
                    $lines[] = '$this->load->repository(\'' . $foreign . '\');';
                }

                $lines[] = '$this->load->repository(\'' . $model . '\');';
            }

            $lines[] = '// ------------------------------------';

            if ($type === self::TYPE_DOCTRINE)
            {
                $lines[] = '';
                $lines[] = '// Load the required entity repositories ---';
                $lines[] = '$credo = new Credo($this->db);';

                $foreigns[] = $model;

                foreach ($foreigns as $foreign)
                {
                    $foreign = ucfirst($foreign);

                    $lines[] = '';
                    $lines[] = '/** @var \\' . $foreign . '_repository */';
                    $lines[] = '$repo = $credo->get_repository(\'' . $foreign . '\');';
                    $lines[] = '$this->' . strtolower($foreign) . '_repo = $repo;';
                }

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
            $lines[] = '$data = array();';

            $lines = $this->setForeigns($lines);

            $lines[] = '';
            $lines[] = 'if (! $input)';
            $lines[] = '{';

            // TODO: Show if --with-view enabled -------------------------
            if ($this->layout)
            {
                $lines[] = '    $this->load->view(\'layout/header\');';
            }

            $lines[] = '    $this->load->view(\'' . $name . '/create\', $data);';

            if ($this->layout)
            {
                $lines[] = '    $this->load->view(\'layout/footer\');';
            }

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
                $lines[] = '$exists = $this->' . $model . '_repo->exists($input);';
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

            // TODO: Show if --with-view enabled --------------------------------
            if ($this->layout)
            {
                $lines[] = '    $this->load->view(\'layout/header\');';
            }

            $lines[] = '    $this->load->view(\'' . $name . '/create\', $data);';

            if ($this->layout)
            {
                $lines[] = '    $this->load->view(\'layout/footer\');';
            }

            $lines[] = '';
            // ------------------------------------------------------------------

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

                $lines[] = '$this->' . $model . '_repo->create($input, new ' . $class . ');';
                $lines[] = '';
                $lines[] = '$this->' . $model . '_repo->flush();';
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
                $lines[] = '$item = $this->' . $model . '_repo->find($id);';
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
                $lines[] = '$this->' . $model . '_repo->delete($item);';
                $lines[] = '';
                $lines[] = '$this->' . $model . '_repo->flush();';
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
                $lines[] = 'if (! $item = $this->' . $model . '_repo->find($id))';
            }

            $lines[] = '{';
            $lines[] = '    show_404();';
            $lines[] = '}';
            $lines[] = '';
            $lines[] = '/** @var \\' . ucfirst($model) . ' $item */';
            $lines[] = '$data = array(\'item\' => $item);';
            $lines[] = '// -----------------------------------';

            $lines = $this->setForeigns($lines);

            $lines[] = '';

            $lines[] = '// Skip if provided empty input ---';
            $lines[] = '/** @var array<string, mixed> */';
            $lines[] = '$input = $this->input->post(null, true);';
            $lines[] = '';
            $lines[] = 'if (! $input)';
            $lines[] = '{';

            // TODO: Show if --with-view enabled ------------------------------
            if ($this->layout)
            {
                $lines[] = '    $this->load->view(\'layout/header\');';
            }

            $lines[] = '    $this->load->view(\'' . $name . '/edit\', $data);';

            if ($this->layout)
            {
                $lines[] = '    $this->load->view(\'layout/footer\');';
            }

            $lines[] = '';
            // ----------------------------------------------------------------

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
                $lines[] = '$exists = $this->' . $model . '_repo->exists($input, $id);';
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

            // TODO: Show if --with-view enabled ------------------------------
            if ($this->layout)
            {
                $lines[] = '    $this->load->view(\'layout/header\');';
            }

            $lines[] = '    $this->load->view(\'' . $name . '/edit\', $data);';

            if ($this->layout)
            {
                $lines[] = '    $this->load->view(\'layout/footer\');';
            }

            $lines[] = '';
            // ----------------------------------------------------------------

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
                $lines[] = '$this->' . $model . '_repo->update($item, $input);';
                $lines[] = '';
                $lines[] = '$this->' . $model . '_repo->flush();';
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
     * @param string[] $lines
     *
     * @return string[]
     */
    protected function setForeigns($lines)
    {
        $items = array();

        foreach ($this->cols as $col)
        {
            if ($col->isForeignKey())
            {
                $items[] = $col;
            }
        }

        if (count($items) > 0)
        {
            $lines[] = '';
        }

        foreach ($items as $item)
        {
            $name = $item->getReferencedTable();
            $name = Inflector::plural($name);

            $model = Inflector::singular($name);

            if ($this->type === self::TYPE_DOCTRINE)
            {
                $lines[] = '$data[\'' . $name . '\'] = $this->' . $model . '_repo->dropdown(\'id\');';
            }
            else
            {
                $lines[] = '$data[\'' . $name . '\'] = $this->' . $model . '->get()->dropdown(\'id\');';
            }
        }

        return $lines;
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
                $lines[] = '$total = (int) $this->' . $model . '_repo->total();';
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
                $lines[] = '$items = $this->' . $model . '_repo->get(10, $offset);';
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

            if ($this->layout)
            {
                $lines[] = '$this->load->view(\'layout/header\');';
            }

            $lines[] = '$this->load->view(\'' . $name . '/index\', $data);';

            if ($this->layout)
            {
                $lines[] = '$this->load->view(\'layout/footer\');';
            }
            // -------------------------------------------------------------

            return $lines;
        });

        $this->addMethod($method);
    }
}
