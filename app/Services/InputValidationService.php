<?php

namespace App\Services;

use App\Models\User;

class InputValidationService
{
    private string $errorMessage = "";
    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    public function nameValid(): bool
    {
        if (strlen($this->user->getName()) < 4) {
            $this->errorMessage = "Username cannot be shorter than 4 characters!";
            return false;
        }
        if (strlen($this->user->getName()) > 100) {
            $this->errorMessage = "Username cannot be longer than 100 characters!";
            return false;
        }
        if (!ctype_alnum($this->user->getName())) {
            $this->errorMessage = "Username cannot contain characters that are not letters or numbers!";
            return false;
        }
        return true;
    }

    public function emailValid(): bool
    {
        if (!filter_var($this->user->getEmail(), FILTER_VALIDATE_EMAIL)) {
            $this->errorMessage = "Invalid e-mail address!";
        }
        if (strlen($this->user->getEmail()) > 255) {
            $this->errorMessage = "E-mail cannot be longer than 255 characters!";
            return false;
        }
        return true;
    }

    public function passwordValid(): bool
    {
        if (strlen($this->user->getPassword()) < 6) {
            $this->errorMessage = "Password cannot be shorter than 6 characters!";
            return false;
        }
        if (strlen($this->user->getPassword()) > 255) {
            $this->errorMessage = "Password cannot be longer than 255 characters!";
            return false;
        }
        if (!ctype_graph($this->user->getPassword())) {
            $this->errorMessage = "Password cannot contain special characters!";
            return false;
        }
        return true;
    }

    public function passwordRepeatedValid(): bool
    {
        if ($this->user->getPassword() !== $this->user->getPasswordRepeated()) {
            $this->errorMessage = "Passwords do not match!";
            return false;
        }
        return true;
    }
}