<?php

namespace App\Controllers;

use App\{Database, Redirect, Template, Validation};
use App\Models\User;

class UserRegisterController
{
    public function displayRegisterForm(): Template
    {
        return new Template("templates/register.twig");
    }

    public function store(): Redirect
    {
        $password = $_POST["password"] ?? "";
        $email = $_POST["email"] ?? "";
        $name = $_POST["name"] ?? "";
        $passwordRepeated = $_POST["passwordRepeated"] ?? "";
        $user = new User($password, $email, $name, $passwordRepeated);

        $validation = new Validation($user);
        if (
            !$validation->isNameValid() ||
            !$validation->isEmailValid() ||
            !$validation->isPasswordValid() ||
            !$validation->isPasswordRepeatedValid() ||
            !$validation->isEmailTaken()
        ) {
            return new Redirect("/register");
        }

        Database::insert($user);
        if (!empty($_SESSION["errors"])) {
            return new Redirect("/register");
        }
        return new Redirect("/login");
    }
}