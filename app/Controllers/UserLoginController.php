<?php

namespace App\Controllers;

use App\Template;
use App\Models\User;
use App\Services\InputValidationService;
use function App\loadUserService;

class UserLoginController
{
    public function displayLoginForm(): Template
    {
        if (isset($_SESSION["id"])) {
            header("Location: /");
            exit();
        }
        return new Template("templates/login.twig");
    }

    public function login(): Template
    {
        $password = $_POST["password"] ?? "";
        $email = $_POST["email"] ?? "";
        $user = new User($password, $email);

        $userValidate = new InputValidationService($user);
        if (!$userValidate->emailValid() || !$userValidate->passwordValid()) {
            return new Template("templates/login.twig", [
                "loginErrorMessage" => $userValidate->getErrorMessage(),
                "email" => $email,
            ]);
        }

        $database = loadUserService();
        if ($database->getErrorMessage() !== "") {
            return new Template("templates/login.twig", [
                "errorMessage" => $database->getErrorMessage(),
            ]);
        }

        $id = $database->authenticate($user);
        if ($database->getErrorMessage() !== "") {
            return new Template("templates/login.twig", [
                "loginErrorMessage" => $database->getErrorMessage(),
                "email" => $email,
            ]);
        }

        $_SESSION["id"] = $id;
        header("Location: /");
        exit();
    }

    public function logout(): Template
    {
        if (isset($_SESSION["id"])) {
            unset($_SESSION["id"]);
            header("Location: /");
            exit();
        }
        return new Template("templates/logout.twig", [
            "errorMessage" => "Not logged in!",
        ]);
    }
}