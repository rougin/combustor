<?php

namespace Rougin\Combustor\Template;

use Rougin\Combustor\Inflector;

/**
 * @package Combustor
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class IndexPlate
{
    /**
     * @var boolean
     */
    protected $bootstrap = false;

    /**
     * @var \Rougin\Describe\Column[]
     */
    protected $cols;

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
     * @param integer                   $type
     * @param \Rougin\Describe\Column[] $cols
     */
    public function __construct($table, $type, $cols)
    {
        $this->type = $type;

        $this->table = $table;

        $this->cols = $cols;
    }

    /**
     * @param string $tab
     *
     * @return string
     */
    public function make($tab = '')
    {
        $model = Inflector::singular($this->table);

        $route = Inflector::plural($this->table);

        $lines = array('<h1>' . ucfirst($route) . '</h1>');
        $lines[] = '';

        $lines[] = '<?php if (isset($alert)): ?>';
        $lines[] = '  <div class=""><?= $alert ?></div>';
        $lines[] = '<?php endif ?>';
        $lines[] = '';

        $text = 'Create New ' . ucfirst($model);
        $lines[] = '<div class="">';
        $lines[] = '  <a class="" href="<?= base_url(\'' . $route . '/create\') ?>">' . $text . '</a>';
        $lines[] = '</div>';
        $lines[] = '';

        $lines[] = '<div>';

        $table = new TablePlate($this->table, $this->cols, $this->type);

        $table->withBootstrap($this->bootstrap);

        foreach ($table->make($tab) as $line)
        {
            $lines[] = $line;
        }

        $lines[] = '  <?= $links ?>';
        $lines[] = '</div>';
        $lines[] = '';

        $lines[] = '<script>';
        $lines[] = '  trash = function (self)';
        $lines[] = '  {';
        $lines[] = '    const text = \'Do you want to delete the selected ' . $model . '?\'';
        $lines[] = '';
        $lines[] = '    if (confirm(text))';
        $lines[] = '    {';
        $lines[] = '      self.submit()';
        $lines[] = '    }';
        $lines[] = '  }';
        $lines[] = '</script>';

        $result = implode("\n", $lines);

        return str_replace(' class=""', '', $result);
    }

    /**
     * @param boolean $bootstrap
     *
     * @return self
     */
    public function withBootstrap($bootstrap)
    {
        $this->bootstrap = $bootstrap;

        return $this;
    }
}
