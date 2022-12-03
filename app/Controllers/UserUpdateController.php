<?php

namespace App\Controllers;

use App\Template;
use App\Models\User;
use App\Services\InputValidationService;
use function App\loadUserService;

class UserUpdateController
{
    public function displayAccount(): Template
    {
        if (!isset($_SESSION["id"])) {
            return new Template("base.twig", [
                "errorMessage" => "Not logged in!",
            ]);
        }

        $database = loadUserService();
        if ($database->getErrorMessage() !== "") {
            return new Template("templates/account.twig", [
                "errorMessage" => $database->getErrorMessage(),
            ]);
        }

        return new Template("templates/account.twig", [
            "name" => $database->getNameFromId($_SESSION["id"]),
            "email" => $database->getEmailFromId($_SESSION["id"]),
        ]);
    }

    public function update(): Template
    {
        if (!isset($_SESSION["id"])) {
            header("Location: /login");
            exit();
        }

        $password = $_POST["password"] ?? "";
        $name = $_POST["name"] ?? "";
        $email = $_POST["email"] ?? "";
        $user = new User($password, $email, $name);

        $userValidate = new InputValidationService($user);
        if (!$userValidate->nameValid() ||
            !$userValidate->emailValid() ||
            !$userValidate->passwordValid()
        ) {
            return new Template("templates/account.twig", [
                "updateErrorMessage" => $userValidate->getErrorMessage(),
                "name" => $name,
                "email" => $email,
            ]);
        }

        $database = loadUserService();
        if ($database->getErrorMessage() !== "") {
            return new Template("templates/account.twig", [
                "errorMessage" => $database->getErrorMessage(),
            ]);
        }

        $database->update($_SESSION["id"], $user);
        if ($database->getErrorMessage() !== "") {
            return new Template("templates/account.twig", [
                "updateErrorMessage" => $database->getErrorMessage(),
                "name" => $name,
                "email" => $email,
            ]);
        }

        header("Location: /");
        exit();
    }

    public function updatePassword(): Template
    {
        if (!isset($_SESSION["id"])) {
            header("Location: /login");
            exit();
        }

        $passwordCurrent = $_POST["passwordCurrent"] ?? "";
        $passwordNew = $_POST["passwordNew"] ?? "";
        $passwordNewRepeated = $_POST["passwordNewRepeated"] ?? "";
        $newUser = new User($passwordNew, "", "", $passwordNewRepeated);
        $user = new User($passwordCurrent);

        $database = loadUserService();
        if ($database->getErrorMessage() !== "") {
            return new Template("templates/account.twig", [
                "errorMessage" => $database->getErrorMessage(),
            ]);
        }

        $userValidate = new InputValidationService($newUser);
        if (!$userValidate->passwordValid() || !$userValidate->passwordRepeatedValid()) {
            return new Template("templates/account.twig", [
                "passwordErrorMessage" => $userValidate->getErrorMessage(),
                "name" => $database->getNameFromId($_SESSION["id"]),
                "email" => $database->getEmailFromId($_SESSION["id"]),
            ]);
        }


        $database->updatePassword($_SESSION["id"], $user, $newUser);
        if ($database->getErrorMessage() !== "") {
            return new Template("templates/account.twig", [
                "passwordErrorMessage" => $database->getErrorMessage(),
                "name" => $database->getNameFromId($_SESSION["id"]),
                "email" => $database->getEmailFromId($_SESSION["id"]),
            ]);
        }

        header("Location: /");
        exit();
    }

    public function delete(): Template
    {
        if (!isset($_SESSION["id"])) {
            header("Location: /login");
            exit();
        }
        $passwordForDeletion = $_POST["passwordForDeletion"] ?? "";
        $user = new User($passwordForDeletion);

        $database = loadUserService();
        if ($database->getErrorMessage() !== "") {
            return new Template("templates/account.twig", [
                "errorMessage" => $database->getErrorMessage(),
            ]);
        }

        $database->deleteUser($_SESSION["id"], $user);
        if ($database->getErrorMessage() !== "") {
            return new Template("templates/account.twig", [
                "deleteErrorMessage" => $database->getErrorMessage(),
                "name" => $database->getNameFromId($_SESSION["id"]),
                "email" => $database->getEmailFromId($_SESSION["id"]),
            ]);
        }

        unset($_SESSION["id"]);
        header("Location: /");
        exit();
    }

}