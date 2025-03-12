<?php

namespace App\Observers;

use App\Models\Article;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ArticleObserver
{
    //
    public function created(Article $article)
    {
        $this->clearArticleCache($article);
    }

    public function updated(Article $article)
    {
        $this->clearArticleCache($article);
    }

    public function deleted(Article $article)
    {
        $this->clearArticleCache($article);
    }

    protected function clearArticleCache(Article $article)
    {
        // // Generate the cache key pattern based on the article's attributes
        // $cacheKeyPattern = 'articles_*';

        // // Clear all cache entries that match the pattern
        // Cache::tags('articles')->flush();

        $cacheKeyPattern = 'articles_*';

        // Query the cache table and delete entries that match the pattern
        DB::table('cache')
            ->where('key', 'like', $cacheKeyPattern)
            ->delete();
    }
}
