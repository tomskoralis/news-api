<?php

namespace App;

use Carbon\Carbon;
use Dotenv\Dotenv;
use Dotenv\Exception\ValidationException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use jcobhams\NewsApi\{NewsApi, NewsApiException};

class ApiAccess
{
    private NewsApi $newsApi;
    private string $errorMessage = "";

    public function __construct()
    {
        $dotenv = Dotenv::createImmutable(__DIR__, '../.env');
        $dotenv->load();
        try {
            $dotenv->required('NEWS_API_KEY')->notEmpty();
            $this->newsApi = new NewsApi($_ENV['NEWS_API_KEY']);
        } catch (ValidationException $e) {
            $this->errorMessage = "Validation exception: {$e->getMessage()}";
        } catch (\Exception $e) {
            $this->errorMessage = "General exception: {$e->getMessage()}";
        }
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    public function searchUsingApi(string $searchText, int $page = 1): ?Articles
    {
        if (isset($this->newsApi) && $searchText != "") {
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
//                echo "</pre>"; var_dump($articles->articles); die;
            } catch (NewsApiException $e) {
                $this->errorMessage = "News Api Exception: " . $e->getMessage();
                return null;
            } catch (ClientException $e) {
                $this->errorMessage = "Client Exception: " . $e->getResponse()->getBody()->getContents();
                return null;
            } catch (ServerException $e) {
                $this->errorMessage = "Server Exception: " . $e->getResponse()->getBody()->getContents();
                return null;
            }
            if (isset($articles) && $articles->articles !== []) {
                return new Articles($articles->articles, $articles->totalResults);
            } else {
                $this->errorMessage = "No articles found";
            }
        }
        return null;
    }
}