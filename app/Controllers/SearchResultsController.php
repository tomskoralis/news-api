<?php

namespace App\Controllers;

use App\ApiAccess;
use Twig\Environment;
use Twig\Error\{LoaderError, RuntimeError, SyntaxError};
use const App\PAGE_SIZE;

class SearchResultsController
{
    public function index(Environment $twig, ApiAccess $newsApi): void
    {
        $searchText = $_GET["search"] ?? "";
        $page = (isset($_GET["page"]) && (int)$_GET["page"] > 0) ? (int)$_GET["page"] : 1;
        try {
            if ($searchText) {
                $articles = $newsApi->searchUsingApi($searchText, $page);
                $errorMessage = $newsApi->getErrorMessage();
                echo $twig->render('templates/search.twig', array(
                    'searchText' => $searchText,
                    'articles' => $articles,
                    'page' => $page,
                    'pageSize' => PAGE_SIZE,
                    'errorMessage' => $errorMessage
                ));
            } else {
                echo $twig->render('base.twig');
            }
        } catch (LoaderError $e) {
            echo "Twig Loader Error: " . $e->getMessage();
        } catch (RuntimeError $e) {
            echo "Twig Runtime Error: " . $e->getMessage();
        } catch (SyntaxError $e) {
            echo "Twig Syntax Error: " . $e->getMessage();
        }
    }
}