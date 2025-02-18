<?php

namespace Public;

use Dotenv\Dotenv;

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');

require __DIR__ . '/../app/Routes/web.php';
