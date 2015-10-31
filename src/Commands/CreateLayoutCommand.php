<?php

namespace Rougin\Combustor\Commands;

use Rougin\Blueprint\AbstractCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

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
     * Override this to check for x or y and return false if the command can not
     * run properly under the current conditions.
     *
     * @return bool
     */
    public function isEnabled()
    {
        return Tools::hasLayout();
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
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     * @return object|OutputInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filePath = APPPATH . 'views/layout';

        $data = [];
        $data['bootstrapContainer'] = '';
        $data['scripts'] = [];
        $data['styleSheets'] = [
            '//maxcdn.bootstrapcdn.com/font-awesome/' .
                '4.2.0/css/font-awesome.min.css'
        ];

        $fontAwesome = ! is_dir('bower_components/font-awesome')
            ? system('bower install font-awesome')
            : TRUE;

        if ($fontAwesome) {
            $data['styleSheets'][0] = '<?php echo base_url(\'' .
                'bower_components/font-awesome/css/' .
                'font-awesome.min.css'
                . '\'); ?>';
        }

        if ($input->getOption('bootstrap')) {
            $data['bootstrapContainer'] = 'container';

            $bootstrapCss = 'https://maxcdn.bootstrapcdn.com/bootstrap/' .
                '3.2.0/css/bootstrap.min.css';

            $bootstrapJs = 'https://maxcdn.bootstrapcdn.com/bootstrap/' .
                '3.2.0/css/bootstrap.min.js';

            $jquery = 'https://code.jquery.com/jquery-2.1.1.min.js';

            $bower = ! is_dir('bower_components/bootstrap')
                ? system('bower install bootstrap')
                : TRUE;

            if ($bower) {
                $bootstrapCss = '<?php echo base_url(\'' .
                    'bower_components/bootstrap/dist/css/bootstrap.min.css'
                    . '\'); ?>';

                $bootstrapJs = '<?php echo base_url(\'' .
                    'bower_components/bootstrap/dist/js/bootstrap.min.js'
                    . '\'); ?>';

                $jquery = '<?php echo base_url(\'' .
                    'bower_components/jquery/dist/jquery.min.js'
                    . '\'); ?>';
            }
 
            array_push($data['styleSheets'], $bootstrapCss);
            array_push($data['scripts'], $jquery);
            array_push($data['scripts'], $bootstrapJs);
        }

        if ( ! @mkdir($filePath, 0777, TRUE)) {
            $message = 'The layout directory already exists!';

            return $output->writeln('<error>' . $message . '</error>');
        }

        $header = $this->renderer->render('Views/Layout/Header.template', $data);
        $footer = $this->renderer->render('Views/Layout/Footer.template', $data);

        $headerFile = fopen($filePath . '/header.template', 'wb');
        $footerFile = fopen($filePath . '/footer.template', 'wb');

        file_put_contents($filePath . '/header.template', $header);
        file_put_contents($filePath . '/footer.template', $footer);

        fclose($headerFile);
        fclose($footerFile);

        $message = 'The layout folder has been created successfully!';

        return $output->writeln('<info>' . $message . '</info>');
    }
}
