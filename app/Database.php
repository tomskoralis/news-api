<?php

namespace App;

use App\Models\User;
use Dotenv\Dotenv;
use Doctrine\DBAL\{Connection, DriverManager, Exception};
use Dotenv\Exception\ValidationException;

class Database
{
    private static Connection $connection;

    public static function getConnection(): ?Connection
    {
        if (!isset(self::$connection)) {
            $dotenv = Dotenv::createImmutable(__DIR__, "../.env");
            $dotenv->load();
            $connectionParams = [
                "dbname" => $_ENV["DATABASE_NAME"],
                "user" => $_ENV["DATABASE_USER"],
                "password" => $_ENV["DATABASE_PASSWORD"],
                "host" => $_ENV["DATABASE_HOST"] ?: "localhost",
                "driver" => $_ENV["DATABASE_DRIVER"] ?: "pdo_mysql",
            ];
            try {
                $dotenv->required(["DATABASE_NAME", "DATABASE_USER", "DATABASE_PASSWORD",])->notEmpty();
            } catch (ValidationException $e) {
                $_SESSION["errors"]["database"] = "Dotenv Validation Exception: {$e->getMessage()}";
                return null;
            } catch (\Exception $e) {
                $_SESSION["errors"]["database"] = "Exception: {$e->getMessage()}";
                return null;
            }
            try {
                self::$connection = DriverManager::getConnection($connectionParams);
            } catch (Exception $e) {
                $_SESSION["errors"]["database"] = "Database Exception: " . $e->getMessage();
                return null;
            }
        }
        return self::$connection;
    }

    public static function insert(User $user): void
    {
        $database = Database::getConnection();
        if (!isset($database)) {
            return;
        }
        try {
            $queryBuilder = $database->createQueryBuilder();
            $queryBuilder
                ->insert("users")
                ->values([
                    'name' => '?',
                    'email' => '?',
                    'password' => '?',
                ])
                ->setParameter(0, $user->getName())
                ->setParameter(1, $user->getEmail())
                ->setParameter(2, password_hash($user->getPassword(), PASSWORD_BCRYPT));
            $queryBuilder->executeQuery();
        } catch (Exception $e) {
            $_SESSION["errors"]["database"] = "Database Exception: " . $e->getMessage();
        }
    }

    public static function searchId(User $user): int
    {
        $database = Database::getConnection();
        if (!isset($database)) {
            return 0;
        }
        try {
            $queryBuilder = $database->createQueryBuilder();
            $queryBuilder
                ->select('id')
                ->from('users')
                ->where('email = ?')
                ->setParameter(0, $user->getEmail());
            return $queryBuilder->executeQuery()->fetchAssociative()["id"] ?? 0;
        } catch (Exception $e) {
            $_SESSION["errors"]["database"] = "Database Exception: " . $e->getMessage();
            return 0;
        }
    }

    public static function update(User $user, int $userId): void
    {
        $database = Database::getConnection();
        if (!isset($database)) {
            return;
        }
        try {
            $queryBuilder = $database->createQueryBuilder();
            $queryBuilder
                ->update('users')
                ->set('name', '?')
                ->set('email', '?')
                ->where('id = ?')
                ->setParameter(0, $user->getName())
                ->setParameter(1, $user->getEmail())
                ->setParameter(2, $userId);
            $queryBuilder->executeQuery();
        } catch (Exception $e) {
            $_SESSION["errors"]["database"] = "Database Exception: " . $e->getMessage();
        }
    }

    public static function updatePassword(User $newUser, int $userId): void
    {
        $database = Database::getConnection();
        if (!isset($database)) {
            return;
        }
        try {
            $queryBuilder = $database->createQueryBuilder();
            $queryBuilder
                ->update('users')
                ->set('password', '?')
                ->where('id = ?')
                ->setParameter(0, password_hash($newUser->getPassword(), PASSWORD_BCRYPT))
                ->setParameter(1, $userId);
            $queryBuilder->executeQuery();
        } catch (Exception $e) {
            $_SESSION["errors"]["database"] = "Database Exception: " . $e->getMessage();
        }
    }

    public static function delete(int $userId): void
    {
        $database = Database::getConnection();
        if (!isset($database)) {
            return;
        }
        try {
            $queryBuilder = $database->createQueryBuilder();
            $queryBuilder
                ->delete('users')
                ->where('id = ?')
                ->setParameter(1, $userId);
            $queryBuilder->executeQuery();
        } catch (Exception $e) {
            $_SESSION["errors"]["database"] = "Database Exception: " . $e->getMessage();
        }
    }
}