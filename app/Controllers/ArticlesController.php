<?php

namespace App\Controllers;

use App\Template;
use App\Services\ApiService;
use Dotenv\Dotenv;
use const App\PAGE_SIZE;

class ArticlesController
{
    public function index(): Template
    {
        $searchText = $_GET["search"] ?? "";
        if ($searchText === "") {
            return new Template("templates/search.twig");
        }
        $page = (isset($_GET["page"]) && (int)$_GET["page"] > 0) ? (int)$_GET["page"] : 1;
        $dotenv = Dotenv::createImmutable(__DIR__, "../../.env");
        $dotenv->load();
        $newsApi = new ApiService($dotenv);
        $articles = $newsApi->searchUsingApi($searchText, $page);
        $errorMessage = $newsApi->getErrorMessage();
        return new Template ("templates/search.twig", [
            "searchText" => $searchText,
            "articles" => $articles,
            "page" => $page,
            "pageSize" => PAGE_SIZE,
            "errorMessage" => $errorMessage
        ]);
    }
}