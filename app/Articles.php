<?php

namespace App;

use App\Models\Article;

class Articles
{
    private array $articles;
    private int $totalResults;

    public function __construct(array $articles, int $totalResults)
    {
        foreach ($articles as $key => $article) {
            if ($key === PAGE_SIZE) break;
            $this->articles [] = $article;
        }
        $this->totalResults = $totalResults;
    }

    public function getArticles(): ?\Generator
    {
        foreach ($this->articles as $article) {
            yield new Article($article);
        }
    }

    public function getTotalResults(): int
    {
        return $this->totalResults;
    }

}