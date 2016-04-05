<?php

namespace Rougin\Combustor\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Rougin\Combustor\Common\Tools;
use Rougin\Combustor\Common\Commands\InstallCommand;

/**
 * Install Wildfire Command
 *
 * Installs Wildfire for CodeIgniter
 * 
 * @package Combustor
 * @author  Rougin Royce Gutib <rougingutib@gmail.com>
 */
class InstallWildfireCommand extends InstallCommand
{
    /**
     * @var string
     */
    protected $library = 'wildfire';

    /**
     * Executes the command.
     * 
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return object|\Symfony\Component\Console\Output\OutputInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Adds Wildfire.php to the "libraries" directory
        $autoload = file_get_contents(APPPATH . 'config/autoload.php');
        $lines = explode(PHP_EOL, $autoload);

        $pattern = '/\$autoload\[\'libraries\'\] = array\((.*?)\)/';

        preg_match_all($pattern, $lines[60], $match);

        $libraries = explode(', ', end($match[1]));

        if ( ! in_array('\'wildfire\'', $libraries)) {
            array_push($libraries, '\'wildfire\'');

            $libraries = array_filter($libraries);

            $pattern = '/\$autoload\[\'libraries\'\] = array\([^)]*\);/';
            $replacement = '$autoload[\'libraries\'] = array(' . implode(', ', $libraries) . ');';

            $lines[60] = preg_replace($pattern, $replacement, $lines[60]);

            file_put_contents(APPPATH . 'config/autoload.php', implode(PHP_EOL, $lines));
        }

        $file = fopen(APPPATH . 'libraries/Wildfire.php', 'wb');
        $wildfire = $this->renderer->render('Libraries/Wildfire.template');

        file_put_contents(APPPATH . 'libraries/Wildfire.php', $wildfire);
        fclose($file);

        Tools::ignite();

        $message = 'Wildfire is now installed successfully!';

        return $output->writeln('<info>'.$message.'</info>');
    }
}
