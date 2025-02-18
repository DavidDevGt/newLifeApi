<?php

namespace Public;

use Dotenv\Dotenv;

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

// CORS
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, X-Api-Key");
header("Access-Control-Allow-Credentials: true");


header('Content-Type: application/json; charset=UTF-8');

require __DIR__ . '/../app/Routes/web.php';
