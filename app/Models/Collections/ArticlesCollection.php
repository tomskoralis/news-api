<?php

namespace App\Models\Collections;

use App\Models\Article;
use const App\PAGE_SIZE;

class ArticlesCollection
{
    private array $articles;
    private int $articleCount;

    public function __construct(array $articles, int $articleCount)
    {
        foreach ($articles as $key => $article) {
            if ($key === PAGE_SIZE) break;
            $this->articles [] = $article;
        }
        $this->articleCount = $articleCount;
    }

    public function getArticles(): \Generator
    {
        foreach ($this->articles as $article) {
            yield new Article($article);
        }
    }

    public function getArticleCount(): int
    {
        return $this->articleCount;
    }
}