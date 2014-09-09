<?php

namespace Combustor;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

class Generator extends Command {

	protected function configure()
	{
		$this->setName("fibonacci")
			->setDescription("Display the fibonacci numbers between 2 given numbers")
			->setDefinition(array(
					new InputOption('start', 's', InputOption::VALUE_OPTIONAL, 'Start number of the range of Fibonacci number', $start),
					new InputOption('stop', 'e', InputOption::VALUE_OPTIONAL, 'stop number of the range of Fibonacci number', $stop)
				))
			->setHelp(<<<EOT
Display the fibonacci numbers between a range of numbers given as parameters

Usage:

<info>php console.php fibonacci 2 18 <env></info>

You can also specify just a number and by default the start number will be 0
<info>php console.php fibonacci 18 <env></info>

If you don't specify a start and a stop number it will set by default [0,100]
<info>php console.php fibonacci<env></info>
EOT
);
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{

		$header_style = new OutputFormatterStyle('white', 'green', array('bold'));
		$output->getFormatter()->setStyle('header', $header_style);

		$start = intval($input->getOption('start'));
		$stop  = intval($input->getOption('stop'));

		if ( ($start >= $stop) || ($start < 0) ) {
		   throw new \InvalidArgumentException('Stop number should be greater than start number');
		}

		$output->writeln('<header>Fibonacci numbers between '.$start.' - '.$stop.'</header>');

		$xnM2 = 0; // set x(n-2)
		$xnM1 = 1;  // set x(n-1)
		$xn = 0; // set x(n)
		$totalFiboNr = 0;
		while ($xnM2 <= $stop)
		{
			if ($xnM2 >= $start)  {
				$output->writeln('<header>'.$xnM2.'</header>');
				$totalFiboNr++;
			}
			$xn = $xnM1 + $xnM2;
			$xnM2 = $xnM1;
			$xnM1 = $xn;

		}
		$output->writeln('<header>Total of Fibonacci numbers found = '.$totalFiboNr.' </header>');
	}
}