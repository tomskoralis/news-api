<?php

namespace App\Controllers;

use App\{Database, Redirect, Template, Validation};
use App\Models\User;

class UserLoginController
{
    public function displayLoginForm(): Template
    {
        return new Template("templates/login.twig");
    }

    public function login(): Redirect
    {
        $password = $_POST["password"] ?? "";
        $email = $_POST["email"] ?? "";
        $user = new User($password, $email);

        $id = Database::searchId($user);
        $validation = new Validation($user);
        if (
            !$validation->isEmailValid() ||
            !$validation->isPasswordValid() ||
            !$validation->isPasswordMatchingHash($id)
        ) {
            return new Redirect("/login");
        }

        $_SESSION["userId"] = $id;
        return new Redirect("/");
    }

    public function logout(): Redirect
    {
        unset($_SESSION["userId"]);
        return new Redirect("/");
    }
}