<?php

namespace App;

use App\Services\UserService;
use Dotenv\Dotenv;

function loadUserService(): UserService
{
    $dotenv = Dotenv::createImmutable(__DIR__, "../.env");
    $dotenv->load();
    return new UserService($dotenv);
}