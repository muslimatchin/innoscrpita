<?php

namespace App\Repositories\Eloquent;

use App\Models\Article;
use App\Repositories\Interfaces\ArticleRepositoryInterface;
use Illuminate\Support\Facades\Cache;

class ArticleRepository implements ArticleRepositoryInterface
{
    protected $model;

    public function __construct(Article $model)
    {
        $this->model = $model;
    }

    public function getArticles(array $filters = [], int $perPage = 15, int $page = 1)
    {
        // Generate a unique cache key based on the filters and pagination parameters
        $cacheKey = $this->generateCacheKey($filters, $perPage, $page);

        // Define the cache duration (e.g., 60 minutes)
        $cacheDuration = now()->addMinutes(60);

        // Attempt to retrieve cached articles
        return Cache::remember($cacheKey, $cacheDuration, function () use ($filters, $perPage) {
            $query = $this->model->query();

            // Filter by categories
            if (!empty($filters['categories'])) {
                $query->whereHas('categories', function ($q) use ($filters) {
                    $q->whereIn('categories.id', $filters['categories']);
                });
            }

            // Filter by sources
            if (!empty($filters['sources'])) {
                $query->whereIn('source_id', $filters['sources']);
            }

            // Filter by authors
            if (!empty($filters['authors'])) {
                $query->whereIn('author_id', $filters['authors']);
            }

            // Paginate the results
            return $query->paginate($perPage);
        });
    }

    private function generateCacheKey(array $filters, int $perPage, int $page): string
    {
        return 'articles_' . md5(json_encode($filters)) . "_per_page_{$perPage}_page_{$page}";
    }
}
