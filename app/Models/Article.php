<?php

namespace App\Models;

use Carbon\Carbon;

class Article
{
    private string $source;
    private string $author;
    private string $title;
    private string $description;
    private string $url;
    private string $urlToImage;
    private string $publishedAt;

    public function __construct(\stdClass $article)
    {
        $this->source = $article->source->name ?? "";
        $this->author = $article->author ?? "";
        $this->title = $article->title ?? "";
        $this->description = strip_tags($article->description) ?? "";
        $this->url = $article->url ?? "";
        $this->urlToImage = $article->urlToImage ?? "";
        $this->publishedAt = $article->publishedAt ?? "";
    }

    public function getSource(): string
    {
        return $this->source;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getUrlToImage(): string
    {
        return $this->urlToImage;
    }

    public function getPublishedAt(): string
    {
        return Carbon::parse($this->publishedAt)->isoFormat("HH:mm D/M/YYYY");
    }
}