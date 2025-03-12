<?php
namespace App\Services;

use App\Models\User;
use App\Repositories\Interfaces\ArticleRepositoryInterface;
use App\Services\Interfaces\ArticleServiceInterface;

class ArticleService implements ArticleServiceInterface
{
    protected $articleRepository;

    public function __construct(ArticleRepositoryInterface $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    public function fetchArticles($userId, array $queryParams)
    {
        $user = User::findOrFail($userId);

        $filters = [
            'categories' => $queryParams['categories'] ?? $user->preferredCategories->pluck('id')->toArray(),
            'sources'    => $queryParams['sources'] ?? $user->preferredSources->pluck('id')->toArray(),
            'authors'    => $queryParams['authors'] ?? $user->preferredAuthors->pluck('id')->toArray(),
        ];

        $perPage = $queryParams['per_page'] ?? 15;
        $page = $queryParams['page'] ?? 1;

        return $this->articleRepository->getArticles($filters, $perPage, $page);
    }
}
