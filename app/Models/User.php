<?php

namespace App\Models;

class User
{
    private string $password;
    private string $email;
    private string $name;
    private string $passwordRepeated;

    public function __construct(string $password, string $email = "", string $name = "", string $passwordRepeated = "")
    {
        $this->password = $password;
        $this->email = $email;
        $this->name = $name;
        $this->passwordRepeated = $passwordRepeated;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPasswordRepeated(): string
    {
        return $this->passwordRepeated;
    }
}