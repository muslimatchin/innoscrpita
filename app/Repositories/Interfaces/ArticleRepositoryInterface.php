<?php

namespace App\Repositories\Interfaces;

interface ArticleRepositoryInterface
{
    public function getArticles(array $filters = []);
}
