#!/usr/bin/env php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Console\Kernel;

$kernel = new Kernel();
$kernel->run($argv);
