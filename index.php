<?php

use App\{ApiAccess, Router};
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

require_once 'vendor/autoload.php';
require_once 'app/constants.php';

$newsApi = new ApiAccess();
$twig = new Environment(new FilesystemLoader(['views', 'views/templates']));
$router = new Router();
$router->handleUri($twig, $newsApi);