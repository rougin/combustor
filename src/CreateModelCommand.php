<?php

namespace Rougin\Combustor;

use Rougin\Combustor\Doctrine\CreateModelCommand as Doctrine;
use Rougin\Combustor\Wildfire\CreateModelCommand as Wildfire;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateModelCommand extends Command
{
    /**
     * Set the configurations of the specified command
     */
    protected function configure()
    {
        $this->setName('create:model')
            ->setDescription('Create a new model')
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'Name of the model'
            )->addOption(
                'camel',
                NULL,
                InputOption::VALUE_NONE,
                'Use the camel case naming convention'
            )->addOption(
                'doctrine',
                NULL,
                InputOption::VALUE_NONE,
                'Generate a model based on Doctrine'
            )->addOption(
                'lowercase',
                NULL,
                InputOption::VALUE_NONE,
                'Keep the first character of the name to lowercase'
            )->addOption(
                'wildfire',
                NULL,
                InputOption::VALUE_NONE,
                'Generate a model based on Wildfire'
            );
    }

    /**
     * Execute the command
     * 
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $wildfireExists = FALSE;
        $doctrineExists = FALSE;

        if (!$input->getOption('doctrine') && !$input->getOption('wildfire')) {
            if (file_exists(APPPATH . 'libraries/Wildfire.php')) {
                $wildfireExists = TRUE;
            }

            if (file_exists(APPPATH . 'libraries/Doctrine.php')) {
                $doctrineExists = TRUE;
            }

            if ($doctrineExists && $wildfireExists) {
                $message = 'Please select --wildfire or --doctrine';

                exit($output->writeln('<error>' . $message . '</error>'));
            } else if ($doctrineExists) {
                $this->installDoctrine($input, $output);
            } else if ($wildfireExists) {
                $this->installWildfire($input, $output);
            } else {
                $message = 'Please install Wildfire or Doctrine!';

                exit($output->writeln('<error>' . $message . '</error>'));
            }
        } else if ($input->getOption('doctrine')) {
            $this->installDoctrine($input, $output);
        } else if ($input->getOption('wildfire')) {
            $this->installWildfire($input, $output);
        }
    }

    /**
     * Install a Doctrine-based model
     * 
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     * @return CreateModelCommand
     */
    protected function installDoctrine($input, $output)
    {
        $command = new Doctrine($input, $output);

        return $command->execute();
    }

    /**
     * Install a Wildfire-based model
     * 
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     * @return CreateModelCommand
     */
    protected function installWildfire($input, $output)
    {
        if ($input->getOption('camel')) {
            $message = 'Wildfire does not support --camel!.';

            exit($output->writeln('<error>' . $message . '</error>'));
        }

        $command = new Wildfire($input, $output);

        return $command->execute();
    }
}
