<?php

namespace App\Services;

use App\Models\User;
use Doctrine\DBAL\{Connection, DriverManager, Exception};
use Dotenv\Dotenv;
use Dotenv\Exception\ValidationException;

class UserService
{
    private string $errorMessage = "";
    private Connection $connection;

    public function __construct(Dotenv $dotenv)
    {
        try {
            $dotenv->required(["DATABASE_NAME", "DATABASE_USER", "DATABASE_PASSWORD",])->notEmpty();
        } catch (ValidationException $e) {
            $this->errorMessage = "Dotenv Validation Exception: {$e->getMessage()}";
        } catch (\Exception $e) {
            $this->errorMessage = "Exception: {$e->getMessage()}";
        }

        $connectionParams = [
            "dbname" => $_ENV["DATABASE_NAME"],
            "user" => $_ENV["DATABASE_USER"],
            "password" => $_ENV["DATABASE_PASSWORD"],
            "host" => $_ENV["DATABASE_HOST"] ?: "localhost",
            "driver" => $_ENV["DATABASE_DRIVER"] ?: "pdo_mysql",
        ];
        try {
            $this->connection = DriverManager::getConnection($connectionParams);
        } catch (Exception $e) {
            $this->errorMessage = "Database Exception: " . $e->getMessage();
        }
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    public function insert(User $user): void
    {
        if (in_array($user->getEmail(), iterator_to_array($this->getAllEmails()))) {
            $this->errorMessage = "This e-mail is already registered!";
            return;
        }
        try {
            $this->connection->insert("users", [
                "name" => $user->getName(),
                "email" => $user->getEmail(),
                "password" => password_hash($user->getPassword(), PASSWORD_BCRYPT),
            ]);
        } catch (Exception $e) {
            $this->errorMessage = "Database Exception: " . $e->getMessage();
        }
    }

    public function authenticate(User $user): int
    {
        if (!in_array($user->getEmail(), iterator_to_array($this->getAllEmails()))) {
            $this->errorMessage = "E-mail address not found!";
            return 0;
        }
        $userId = $this->getId($user);
        if ($this->comparePasswords($userId, $user->getPassword()) && $this->errorMessage === "") {
            return $userId;
        }
        return 0;
    }

    public function getEmailFromId(int $userId): string
    {
        $sql = "SELECT email FROM users WHERE id = :id";
        try {
            $statement = $this->connection->prepare($sql);
            $statement->bindValue("id", $userId);
            return $statement->executeQuery()->fetchAssociative()["email"];
        } catch (Exception $e) {
            $this->errorMessage = "Database Exception: " . $e->getMessage();
        }
        return "";
    }

    public function getNameFromId(int $userId): string
    {
        $sql = "SELECT name FROM users WHERE id = :id";
        try {
            $statement = $this->connection->prepare($sql);
            $statement->bindValue("id", $userId);
            return $statement->executeQuery()->fetchAssociative()["name"];
        } catch (Exception $e) {
            $this->errorMessage = "Database Exception: " . $e->getMessage();
        }
        return "";
    }

    public function update(int $userId, User $user): void
    {
        if (!$this->comparePasswords($userId, $user->getPassword())) {
            return;
        }
        if (
            $user->getName() === $this->getNameFromId($userId) &&
            $user->getEmail() === $this->getEmailFromId($userId)
        ) {
            $this->errorMessage = "The same username and e-mail!";
            return;
        }
        if (
            $user->getEmail() !== $this->getEmailFromId($userId) &&
            in_array($user->getEmail(), iterator_to_array($this->getAllEmails($userId)))
        ) {
            $this->errorMessage = "This e-mail is already registered!";
            return;
        }
        $sql = "UPDATE users SET name = :name, email = :email WHERE id = :id";
        try {
            $statement = $this->connection->prepare($sql);
            $statement->bindValue("name", $user->getName());
            $statement->bindValue("email", $user->getEmail());
            $statement->bindValue("id", $userId);
            $statement->executeQuery();
        } catch (Exception $e) {
            $this->errorMessage = "Database Exception: " . $e->getMessage();
        }
    }

    public function updatePassword(int $userId, User $user, User $newUser): void
    {
        if (!$this->comparePasswords($userId, $user->getPassword())) {
            return;
        }
        $sql = "UPDATE users SET password = :password WHERE id = :id";
        try {
            $statement = $this->connection->prepare($sql);
            $statement->bindValue("password", password_hash($newUser->getPassword(), PASSWORD_BCRYPT));
            $statement->bindValue("id", $userId);
            $statement->executeQuery();
        } catch (Exception $e) {
            $this->errorMessage = "Database Exception: " . $e->getMessage();
        }
    }

    public function deleteUser(int $userId, User $user): void
    {
        if (!$this->comparePasswords($userId, $user->getPassword())) {
            return;
        }
        $sql = "DELETE FROM users WHERE id = :id";
        try {
            $statement = $this->connection->prepare($sql);
            $statement->bindValue("id", $userId);
            $statement->executeQuery();
        } catch (Exception $e) {
            $this->errorMessage = "Database Exception: " . $e->getMessage();
        }
    }

    private function comparePasswords(int $userId, string $password): bool
    {
        $passwordHash = $this->getPasswordHash($userId);
        if ($this->errorMessage !== "") {
            return false;
        }
        if (!password_verify($password, $passwordHash)) {
            $this->errorMessage = "Incorrect password!";
            return false;
        }
        return true;
    }

    private function getPasswordHash($userId): string
    {
        $sql = "SELECT password FROM users WHERE id = :id";
        try {
            $statement = $this->connection->prepare($sql);
            $statement->bindValue("id", $userId);
            return $statement->executeQuery()->fetchAssociative()["password"];
        } catch (Exception $e) {
            $this->errorMessage = "Database Exception: " . $e->getMessage();
        }
        return "";
    }

    private function getId(User $user): int
    {
        $sql = "SELECT id FROM users WHERE email = :email";
        try {
            $statement = $this->connection->prepare($sql);
            $statement->bindValue("email", $user->getEmail());
            return $statement->executeQuery()->fetchAssociative()["id"];
        } catch (Exception $e) {
            $this->errorMessage = "Database Exception: " . $e->getMessage();
            return 0;
        }
    }

    private function getAllEmails(int $userId = 0): \Generator
    {
        $sql = "SELECT email FROM users WHERE id != :id";
        try {
            $statement = $this->connection->prepare($sql);
            $statement->bindValue("id", $userId);
            foreach ($statement->executeQuery()->fetchAllAssociative() as $email) {
                yield $email["email"];
            }
        } catch (Exception $e) {
            $this->errorMessage = "Database Exception: " . $e->getMessage();
        }
    }
}