<?php

use App\Router;

require_once 'vendor/autoload.php';
require_once 'app/constants.php';

$router = new Router();
$router->handleUri();