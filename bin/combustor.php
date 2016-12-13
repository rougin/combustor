<?php

require 'vendor/autoload.php';

$directory = __DIR__ . '/../build'; // TEST DIRECTORY

Rougin\Combustor\Combustor::boot('combustor.yml', null, $directory)->run();
