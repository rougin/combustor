<?php

namespace Rougin\Combustor\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Rougin\Combustor\Common\File;
use Rougin\Combustor\Common\Tools;
use Rougin\Combustor\Generator\ViewGenerator;
use Rougin\Combustor\Validator\ViewValidator;

/**
 * Create View Command
 *
 * Creates a list of views for CodeIgniter
 *
 * @package Combustor
 * @author  Rougin Royce Gutib <rougingutib@gmail.com>
 */
class CreateViewCommand extends AbstractCommand
{
    /**
     * @var string
     */
    protected $command = 'view';

    /**
     * Executes the command.
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return object|\Symfony\Component\Console\Output\OutputInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = Tools::stripTableSchema(plural($input->getArgument('name')));

        if ($input->getOption('keep')) {
            $name = Tools::stripTableSchema($input->getArgument('name'));
        }

        $validator = new ViewValidator($name);

        if ($validator->fails()) {
            $message = $validator->getMessage();

            return $output->writeln('<error>' . $message . '</error>');
        }

        $data = [
            'isBootstrap' => $input->getOption('bootstrap'),
            'isCamel' => $input->getOption('camel'),
            'name' => $input->getArgument('name')
        ];

        $generator = new ViewGenerator($this->describe, $data);

        $result = $generator->generate();

        $results = [
            'create' => $this->renderer->render('Views/create.tpl', $result),
            'edit' => $this->renderer->render('Views/edit.tpl', $result),
            'index' => $this->renderer->render('Views/index.tpl', $result),
            'show' => $this->renderer->render('Views/show.tpl', $result)
        ];

        $filePath = APPPATH . 'views/' . $name;

        $create = new File($filePath . '/create.php');
        $edit = new File($filePath . '/edit.php');
        $index = new File($filePath . '/index.php');
        $show = new File($filePath . '/show.php');

        $create->putContents($results['create']);
        $edit->putContents($results['edit']);
        $index->putContents($results['index']);
        $show->putContents($results['show']);

        $create->close();
        $edit->close();
        $index->close();
        $show->close();

        $message = 'The views folder "' . $name . '" has been created successfully!';

        return $output->writeln('<info>' . $message . '</info>');
    }
}
