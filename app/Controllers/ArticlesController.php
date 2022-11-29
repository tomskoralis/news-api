<?php

namespace App\Controllers;

use App\ApiAccess;
use App\Template;
use const App\PAGE_SIZE;

class ArticlesController
{
    public function index(): Template
    {
        $searchText = $_GET["search"] ?? "";
        if ($searchText) {
            $newsApi = new ApiAccess();
            $page = (isset($_GET["page"]) && (int)$_GET["page"] > 0) ? (int)$_GET["page"] : 1;
            $articles = $newsApi->searchUsingApi($searchText, $page);
            $errorMessage = $newsApi->getErrorMessage();
            return new Template ('templates/search.twig', [
                'searchText' => $searchText,
                'articles' => $articles,
                'page' => $page,
                'pageSize' => PAGE_SIZE,
                'errorMessage' => $errorMessage
            ]);
        }
        return new Template('base.twig');
    }
}