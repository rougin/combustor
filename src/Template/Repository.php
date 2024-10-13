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
class Repository extends Classidy
{
    /**
     * @var \Rougin\Describe\Column[]
     */
    protected $cols;

    /**
     * @var string[]
     */
    protected $excluded = array();

    /**
     * @param string                    $table
     * @param \Rougin\Describe\Column[] $cols
     * @param string[]                  $excluded
     */
    public function __construct($table, $cols, $excluded = array())
    {
        $this->cols = $cols;

        $this->excluded = $excluded;

        $this->init($table);
    }

    /**
     * @param string $table
     *
     * @return void
     */
    public function init($table)
    {
        $model = ucfirst(Inflector::singular($table));

        $this->setName($model . '_repository');
        $this->extendsTo('Rougin\Credo\Repository');

        $comment = '@extends \Rougin\Credo\Repository<\\' . $model . '>';
        $this->setComment($comment);

        $ci = 'CI_DB_query_builder';
        $this->addClassProperty('db', $ci)->asTag();

        $em = 'Doctrine\ORM\EntityManagerInterface';
        $this->addClassProperty('_em', $em)->asTag();

        $method = new Method('create');
        $method->addArrayArgument('data', 'array<string, mixed>');
        $method->addClassArgument('entity', '\\' . $model);
        $this->addMethod($method->asTag());

        $method = new Method('delete');
        $method->addClassArgument('entity', '\\' . $model);
        $this->addMethod($method->asTag());

        $method = new Method('find');
        $method->setReturn('\\' . $model . '|null');
        $method->addIntegerArgument('id');
        $this->addMethod($method->asTag());

        $method = new Method('get');
        $method->setReturn('\\' . $model . '[]');
        $method->addIntegerArgument('limit', true);
        $method->addIntegerArgument('offset', true);
        $this->addMethod($method->asTag());

        $method = new Method('set');
        $method->setReturn('\\' . $model);
        $method->addArrayArgument('data', 'array<string, mixed>');
        $method->addClassArgument('entity', '\\' . $model);
        $method->addIntegerArgument('id', true);
        $this->addMethod($method->asTag());

        $method = new Method('update');
        $method->addClassArgument('entity', '\\' . $model);
        $method->addArrayArgument('data', 'array<string, mixed>');
        $this->addMethod($method->asTag());

        $this->setExistsMethod();

        $this->setSetMethod($table);
    }

    /**
     * @return void
     */
    protected function setExistsMethod()
    {
        $method = new Method('exists');

        $method->setReturn('boolean');

        $method->addArrayArgument('data', 'array<string, mixed>');

        $method->addIntegerArgument('id', true);

        $method->setCodeLine(function ($lines)
        {
            $lines[] = '// Specify logic here if applicable ---';
            $lines[] = '// ------------------------------------';
            $lines[] = '';
            $lines[] = 'return false;';

            return $lines;
        });

        $this->addMethod($method);
    }

    /**
     * @param string $table
     *
     * @return void
     */
    protected function setSetMethod($table)
    {
        $model = ucfirst(Inflector::singular($table));

        $method = new Method('set');

        $method->setReturn('\\' . $model);
        $method->addArrayArgument('data', 'array<string, mixed>');
        $method->addClassArgument('entity', $model)
            ->withoutTypeDeclared();
        $method->addIntegerArgument('id', true);

        $cols = $this->cols;

        $method->setCodeLine(function ($lines) use ($cols)
        {
            $lines[] = '// List editable fields from table ---';

            foreach ($cols as $index => $col)
            {
                $name = $col->getField();

                if ($col->isPrimaryKey() || in_array($name, $this->excluded))
                {
                    continue;
                }

                $type = $col->getDataType();

                if ($col->isNull())
                {
                    $type = $type . '|null';
                }

                $lines[] = '/** @var ' . $type . ' */';
                $lines[] = '$' . $name . ' = $data[\'' . $name . '\'];';

                $isNull = $col->isNull();

                $space = $isNull ? '    ' : '';

                if ($isNull)
                {
                    $lines[] = 'if ($' . $name . ')';
                    $lines[] = '{';
                }

                if ($col->isForeignKey())
                {
                    $foreign = $col->getReferencedTable();
                    $foreign = Inflector::singular($foreign);

                    $class = ucfirst($foreign);

                    $lines[] = $space . '$user = $this->_em->find(\'' . $class . '\', $' . $name . ');';

                    $name = $foreign;
                }

                $lines[] = $space . '$entity->set_' . $name . '($' . $name . ');';

                if ($isNull)
                {
                    $lines[] = '}';
                }

                if (array_key_exists($index + 1, $cols))
                {
                    $next = $cols[$index + 1];

                    if (! in_array($next->getField(), $this->excluded))
                    {
                        $lines[] = '';
                    }
                }
            }

            $lines[] = '// -----------------------------------';
            $lines[] = '';
            $lines[] = 'return $entity;';

            return $lines;
        });

        $this->addMethod($method);
    }
}
