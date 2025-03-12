<?php

namespace Tests\Unit\Repositories;

use App\Models\Article;
use App\Models\Category;
use App\Models\Source;
use App\Repositories\Eloquent\ArticleRepository;
use App\Repositories\Interfaces\ArticleRepositoryInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Mockery;
use Tests\TestCase;

class ArticleRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected $articleRepository;

    protected function setUp(): void
    {
        parent::setUp();

        // Instantiate the ArticleRepository
        $this->articleRepository = new ArticleRepository(new Article());
    }

    public function test_get_articles_without_filters()
    {
        // Create articles
        $articles = Article::factory()->count(5)->create();

        // Set up cache to return the articles
        Cache::shouldReceive('remember')
            ->once()
            ->with(Mockery::type('string'), Mockery::type('object'), Mockery::type('Closure'))
            ->andReturn($articles);

        // Call the repository method
        $result = $this->articleRepository->getArticles([], 15, 1);

        // Assert that the result matches the articles
        $this->assertEquals($articles, $result);
    }

    public function test_get_articles_with_filters()
    {
        // Create categories, sources, and articles
        $category = Category::factory()->create();
        $source = Source::factory()->create();
        $articles = Article::factory()->count(5)->create();

        // Apply filters
        $filters = [
            'categories' => [$category->id],
            'sources' => [$source->id],
        ];

        // Set up cache to return the filtered articles
        Cache::shouldReceive('remember')
            ->once()
            ->with(Mockery::type('string'), Mockery::type('object'), Mockery::type('Closure'))
            ->andReturn($articles);

        // Call the repository method with filters
        $result = $this->articleRepository->getArticles($filters, 15, 1);

        // Assert that the result matches the filtered articles
        $this->assertEquals($articles, $result);
    }
}
