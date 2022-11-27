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
//    private string $content;

    public function __construct(\stdClass $article)
    {
        $this->source = $article->source->name ?? "";
        $this->author = $article->author ?? "";
        $this->title = $article->title ?? "";
        $article->description = preg_replace("/<(.*?)>/", "", $article->description);
        $article->description = preg_replace("/(http)\S+/", "", $article->description);
        $this->description = $article->description ?? "";
        $this->url = $article->url ?? "";
        $this->urlToImage = $article->urlToImage ?? "";
        $article->publishedAt = Carbon::parse($article->publishedAt)->isoFormat('HH:mm D/M/YYYY');
        $this->publishedAt = $article->publishedAt ?? "";
//        $article->content = preg_replace("/<(.*?)>/", "", $article->content);
//        $article->content = preg_replace("/(http)\S+/", "", $article->content);
//        $this->content = $article->content ?? "";
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
        return $this->publishedAt;
    }

//    public function getContent(): string
//    {
//        return $this->content;
//    }
}