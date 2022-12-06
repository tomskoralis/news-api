<?php

namespace App\ViewVariables;

use App\Database;
use Doctrine\DBAL\Exception;

class AuthViewVariables implements ViewVariablesInterface
{
    public function getName(): string
    {
        return "auth";
    }

    public function getValue(): array
    {
        if (!isset($_SESSION["userId"])) {
            return [];
        }
        $database = Database::getConnection();
        if (!isset($database)) {
            return [];
        }
        try {
            $queryBuilder = $database->createQueryBuilder();
            $queryBuilder
                ->select('*')
                ->from('users')
                ->where('id = ?')
                ->setParameter(0, $_SESSION["userId"]);
            $user = $queryBuilder->executeQuery()->fetchAssociative() ?? [];

        } catch (Exception $e) {
            $_SESSION["errors"]["database"] = "Database Exception: " . $e->getMessage();
            return [];
        }
        return [
            "userId" => $user["id"],
            "name" => $user["name"],
            "email" => $user["email"],
        ];
    }
}