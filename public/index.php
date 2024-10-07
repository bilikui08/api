<?php

ini_set("date.timezone", "America/Argentina/Buenos_Aires");
ini_set('display_errors', 1);
error_reporting(E_ALL ^ E_NOTICE);

require_once '../vendor/autoload.php';

use Dotenv\Dotenv;
use Src\Infrastructure\Http\Request;
use Src\Infrastructure\Router;

Dotenv::createUnsafeImmutable(__DIR__ . '/../')->load();

$routerFile = getenv('ROUTES_FILE');

try {
    $routes = json_decode(file_get_contents($routerFile), true);
    $route = new Router(Request::createRequest(), $routes['routes']);
    echo $route->dispatch();
} catch (Throwable $t) {
    echo $t->getMessage() . ' in file ' . $t->getFile() . ' at line ' . $t->getLine();
    echo "\n\r";
    echo $t->getTraceAsString();
}


