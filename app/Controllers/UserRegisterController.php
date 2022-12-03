<?php

namespace App\Controllers;

use App\Template;
use App\Models\User;
use App\Services\InputValidationService;
use function App\loadUserService;

class UserRegisterController
{
    public function displayRegisterForm(): Template
    {
        if (isset($_SESSION["id"])) {
            header("Location: /");
            exit();
        }
        return new Template("templates/register.twig");
    }

    public function store(): Template
    {
        $password = $_POST["password"] ?? "";
        $email = $_POST["email"] ?? "";
        $name = $_POST["name"] ?? "";
        $passwordRepeated = $_POST["passwordRepeated"] ?? "";
        $user = new User($password, $email, $name, $passwordRepeated);

        $userValidate = new InputValidationService($user);
        if (!$userValidate->nameValid() ||
            !$userValidate->emailValid() ||
            !$userValidate->passwordValid() ||
            !$userValidate->passwordRepeatedValid()
        ) {
            return new Template("templates/register.twig", [
                "registerErrorMessage" => $userValidate->getErrorMessage(),
                "name" => $name,
                "email" => $email,
            ]);
        }

        $database = loadUserService();
        if ($database->getErrorMessage() !== "") {
            return new Template("templates/register.twig", [
                "errorMessage" => $database->getErrorMessage(),
            ]);
        }
        $database->insert($user);
        if ($database->getErrorMessage() !== "") {
            return new Template("templates/register.twig", [
                "registerErrorMessage" => $database->getErrorMessage(),
                "name" => $name,
                "email" => $email,
            ]);
        }

        header("Location: /login");
        exit();
    }
}