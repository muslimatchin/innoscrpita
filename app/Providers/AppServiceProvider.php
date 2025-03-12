<?php

namespace App\Providers;

use App\Models\Article;
use App\Models\User;
use App\Observers\ArticleObserver;
use App\Observers\UserObserver;
use App\Repositories\Eloquent\ArticleRepository;
use App\Repositories\Interfaces\ArticleRepositoryInterface;
use App\Services\ArticleService;
use App\Services\Interfaces\ArticleServiceInterface;
use App\Services\Interfaces\PreferenceServiceInterface;
use App\Services\PreferenceService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        $this->app->bind(PreferenceServiceInterface::class,PreferenceService::class);

        $this->app->bind(ArticleRepositoryInterface::class,ArticleRepository::class);
        $this->app->bind(ArticleServiceInterface::class,ArticleService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        User::observe(UserObserver::class);
        Article::observe(ArticleObserver::class);
    }
}
