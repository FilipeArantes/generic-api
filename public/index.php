<?php

use App\Config\DotEnvConfiguration;
use App\Core\Cors;
use App\Routes\Router;

header('Content-Type: application/json');

require_once './../vendor/autoload.php';

Cors::configureCorsHeaders();
DotEnvConfiguration::loadEnv(__DIR__ . '/../.env');

$router = new Router();
$router->execute($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);