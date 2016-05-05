<?php

namespace Rougin\Combustor\Commands;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Rougin\Combustor\Common\File;
use Rougin\Combustor\Common\Tools;

/**
 * Create Layout Command
 *
 * Creates a new header and footer file for CodeIgniter
 * 
 * @package Combustor
 * @author  Rougin Royce Gutib <rougingutib@gmail.com>
 */
class CreateLayoutCommand extends AbstractCommand
{
    /**
     * Checks whether the command is enabled or not in the current environment.
     *
     * @return bool
     */
    public function isEnabled()
    {
        return ! Tools::hasLayout();
    }

    /**
     * Sets the configurations of the specified command.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('create:layout')
            ->setDescription('Creates a new header and footer file')
            ->addOption(
                'bootstrap',
                NULL,
                InputOption::VALUE_NONE,
                'Includes the Bootstrap CSS/JS Framework tags'
            );
    }

    /**
     * Executes the command.
     * 
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return object|\Symfony\Component\Console\Output\OutputInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $layoutPath = APPPATH . 'views/layout';

        $data = [];

        $data['bootstrapContainer'] = '';
        $data['scripts'] = [];
        $data['styleSheets'] = [];

        $css = '//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css';

        if ( ! is_dir('bower_components/font-awesome') && system('bower install font-awesome')) {
            $css = '<?php echo base_url(\'bower_components/font-awesome/css/font-awesome.min.css\'); ?>';
        }

        $data['styleSheets'][0] = $css;

        if ($input->getOption('bootstrap')) {
            $data['bootstrapContainer'] = 'container';

            $css = 'https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css';
            $js = 'https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.js';
            $jquery = 'https://code.jquery.com/jquery-2.1.1.min.js';

            if ( ! is_dir('bower_components/bootstrap') && system('bower install bootstrap')) {
                $css = '<?php echo base_url(\'bower_components/bootstrap/dist/css/bootstrap.min.css\'); ?>';
                $js = '<?php echo base_url(\'bower_components/bootstrap/dist/js/bootstrap.min.js\'); ?>';
                $jquery = '<?php echo base_url(\'bower_components/jquery/dist/jquery.min.js\'); ?>';
            }
 
            array_push($data['styleSheets'], $css);
            array_push($data['scripts'], $jquery);
            array_push($data['scripts'], $js);
        }

        if ( ! @mkdir($layoutPath, 0777, true)) {
            $message = 'The layout directory already exists!';

            return $output->writeln('<error>' . $message . '</error>');
        }

        $header = $this->renderer->render('Views/Layout/header.tpl', $data);
        $footer = $this->renderer->render('Views/Layout/footer.tpl', $data);

        $headerFile = new File($layoutPath . '/header.php');
        $footerFile = new File($layoutPath . '/footer.php');

        $headerFile->putContents($header);
        $footerFile->putContents($footer);

        $headerFile->close();
        $footerFile->close();

        $message = 'The layout folder has been created successfully!';

        return $output->writeln('<info>' . $message . '</info>');
    }
}
