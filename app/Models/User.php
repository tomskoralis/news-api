<?php

namespace App\Models;

class User
{
    private string $name;
    private string $password;
    private string $email;
    private string $passwordRepeated;

    public function __construct(string $password, string $email = "", string $name = "", string $passwordRepeated = "")
    {
        $this->email = $email;
        $this->password = $password;
        $this->name = $name;
        $this->passwordRepeated = $passwordRepeated;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPasswordRepeated(): string
    {
        return $this->passwordRepeated;
    }
}