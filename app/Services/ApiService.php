<?php

namespace App\Services;

use App\Models\Collections\ArticlesCollection;
use Dotenv\Dotenv;
use Dotenv\Exception\ValidationException;
use GuzzleHttp\Exception\{ClientException, ServerException};
use jcobhams\NewsApi\{NewsApi, NewsApiException};
use const App\{LANGUAGE, PAGE_SIZE};

require_once '../app/constants.php';

class ApiService
{
    private NewsApi $newsApi;
    private string $errorMessage = "";

    public function __construct(Dotenv $dotenv)
    {
        try {
            $dotenv->required("NEWS_API_KEY")->notEmpty();
            $this->newsApi = new NewsApi($_ENV["NEWS_API_KEY"]);
        } catch (ValidationException $e) {
            $this->errorMessage = "Dotenv Validation Exception: {$e->getMessage()}";
        } catch (\Exception $e) {
            $this->errorMessage = "Exception: {$e->getMessage()}";
        }
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    public function searchUsingApi(string $searchText, int $page = 1): ArticlesCollection
    {
        if (!isset($this->newsApi)) {
            return new ArticlesCollection();
        }
        try {
            $articles = $this->newsApi->getEverything(
                $searchText,
                null,
                null,
                null,
                null,
                null,
                LANGUAGE,
                null,
                PAGE_SIZE,
                $page
            );
        } catch (NewsApiException $e) {
            $this->errorMessage = "News Api Exception: " . $e->getMessage();
            return new ArticlesCollection();
        } catch (ClientException $e) {
            $this->errorMessage = "Client Exception: " . $e->getResponse()->getBody()->getContents();
            return new ArticlesCollection();
        } catch (ServerException $e) {
            $this->errorMessage = "Server Exception: " . $e->getResponse()->getBody()->getContents();
            return new ArticlesCollection();
        }
        if (isset($articles) && $articles->articles !== []) {
            return new ArticlesCollection($articles->articles, $articles->totalResults);
        }
        $this->errorMessage = "No articles found";
        return new ArticlesCollection();
    }
}