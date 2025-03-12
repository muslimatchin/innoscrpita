<?php
namespace App\Services\Interfaces;

interface ArticleServiceInterface
{
    public function fetchArticles($userId, array $queryParams);
}
