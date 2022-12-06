<?php

namespace App;

use App\Models\User;
use Doctrine\DBAL\Exception;

class Validation
{
    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function isNameValid(): bool
    {
        if (strlen($this->user->getName()) < 4) {
            $_SESSION["errors"]["name"] = "Username cannot be shorter than 4 characters!";
            return false;
        }
        if (strlen($this->user->getName()) > 100) {
            $_SESSION["errors"]["name"] = "Username cannot be longer than 100 characters!";
            return false;
        }
        if (!ctype_alnum($this->user->getName())) {
            $_SESSION["errors"]["name"] = "Username cannot contain characters that are not letters or numbers!";
            return false;
        }
        return true;
    }

    public function isEmailValid(): bool
    {
        if (!filter_var($this->user->getEmail(), FILTER_VALIDATE_EMAIL)) {
            $_SESSION["errors"]["email"] = "Invalid e-mail address!";
            return false;
        }
        if (strlen($this->user->getEmail()) > 255) {
            $_SESSION["errors"]["email"] = "E-mail cannot be longer than 255 characters!";
            return false;
        }
        return true;
    }

    public function isEmailTaken(int $userId = 0): bool
    {
        $database = Database::getConnection();
        if (!isset($database)) {
            return false;
        }
        try {
            $queryBuilder = $database->createQueryBuilder();
            $queryBuilder
                ->select('email')
                ->from('users')
                ->where('id != ?')
                ->setParameter(0, $userId);
            foreach ($queryBuilder->executeQuery()->fetchAllAssociative() as $email) {
                if ($email["email"] === $this->user->getEmail()) {
                    $_SESSION["errors"]["email"] = "This e-mail is already registered!";
                    return false;
                }
            }
        } catch (Exception $e) {
            $_SESSION["errors"]["database"] = "Database Exception: " . $e->getMessage();
        }
        return true;
    }

    public function isPasswordValid(): bool
    {
        if (strlen($this->user->getPassword()) < 6) {
            $_SESSION["errors"]["password"] = "Password cannot be shorter than 6 characters!";
            return false;
        }
        if (strlen($this->user->getPassword()) > 255) {
            $_SESSION["errors"]["password"] = "Password cannot be longer than 255 characters!";
            return false;
        }
        if (!ctype_graph($this->user->getPassword())) {
            $_SESSION["errors"]["password"] = "Password cannot contain special characters!";
            return false;
        }
        return true;
    }

    public function isPasswordMatchingHash(int $userId = 0, $form = ""): bool
    {
        $database = Database::getConnection();
        if (!isset($database)) {
            return false;
        }
        try {
            $queryBuilder = $database->createQueryBuilder();
            $queryBuilder
                ->select('password')
                ->from('users')
                ->where('id = ?')
                ->setParameter(0, $userId);
            $passwordHash = $queryBuilder->executeQuery()->fetchAssociative()["password"];
        } catch (Exception $e) {
            $_SESSION["errors"]["database"] = "Database Exception: " . $e->getMessage();
            return false;
        }
        if (password_verify($this->user->getPassword(), $passwordHash)) {
            return true;
        }
        $_SESSION["errors"]["passwordMatching" . $form] = "Incorrect password!";
        return false;
    }

    public function isPasswordRepeatedValid(): bool
    {
        if ($this->user->getPassword() !== $this->user->getPasswordRepeated()) {
            $_SESSION["errors"]["passwordRepeated"] = "Passwords do not match!";
            return false;
        }
        return true;
    }
}