<?php

namespace App\Controllers;

use App\{Database, Redirect, Template, Validation};
use App\Models\User;

class UserUpdateController
{
    public function displayAccount(): Template
    {
        return new Template("templates/account.twig");
    }

    public function update(): Redirect
    {
        if (!isset($_SESSION["userId"])) {
            return new Redirect("/login");
        }

        $password = $_POST["password"] ?? "";
        $name = $_POST["name"] ?? "";
        $email = $_POST["email"] ?? "";
        $user = new User($password, $email, $name);

        $validation = new Validation($user);
        if (
            !$validation->isNameValid() ||
            !$validation->isEmailValid() ||
            !$validation->isEmailTaken($_SESSION["userId"]) ||
            !$validation->isPasswordMatchingHash($_SESSION["userId"], "Edit")
        ) {
            return new Redirect("/account");
        }

        Database::update($user, $_SESSION["userId"]);
        if (!empty($_SESSION["errors"])) {
            return new Redirect("/account");
        }
        return new Redirect("/");
    }

    public function updatePassword(): Redirect
    {
        if (!isset($_SESSION["userId"])) {
            return new Redirect("/login");
        }

        $passwordCurrent = $_POST["passwordCurrent"] ?? "";
        $passwordNew = $_POST["passwordNew"] ?? "";
        $passwordNewRepeated = $_POST["passwordNewRepeated"] ?? "";
        $newUser = new User($passwordNew, "", "", $passwordNewRepeated);
        $user = new User($passwordCurrent);

        $validationNew = new Validation($newUser);
        $validation = new Validation($user);
        if (
            !$validationNew->isPasswordValid() ||
            !$validationNew->isPasswordRepeatedValid() ||
            !$validation->isPasswordMatchingHash($_SESSION["userId"], "Password")
        ) {
            return new Redirect("/account");
        }
        Database::updatePassword($newUser, $_SESSION["userId"]);
        if (!empty($_SESSION["errors"])) {
            return new Redirect("/account");
        }

        return new Redirect("/");
    }

    public function delete(): Redirect
    {
        if (!isset($_SESSION["userId"])) {
            return new Redirect("/login");
        }

        $passwordForDeletion = $_POST["passwordForDeletion"] ?? "";
        $user = new User($passwordForDeletion);

        $validation = new Validation($user);
        if (!$validation->isPasswordMatchingHash($_SESSION["userId"], "Delete")) {
            return new Redirect("/account");
        }

        Database::delete($_SESSION["userId"]);
        if (!empty($_SESSION["errors"])) {
            return new Redirect("/account");
        }

        unset($_SESSION["userId"]);
        return new Redirect("/");
    }

}