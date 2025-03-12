<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Models\Article;
use App\Repositories\Interfaces\ArticleRepositoryInterface;
use App\Services\ArticleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Mockery;
use Tests\TestCase;

class ArticleServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $articleRepositoryMock;
    protected $articleService;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock the ArticleRepository
        $this->articleRepositoryMock = Mockery::mock(ArticleRepositoryInterface::class);

        // Instantiate the ArticleService with the mocked repository
        $this->articleService = new ArticleService($this->articleRepositoryMock);
    }

    public function test_fetch_articles_without_filters()
    {
        // Create a user
        $user = User::factory()->create();

        // Define the filters and pagination parameters
        $queryParams = [
            'per_page' => 15,
            'page' => 1,
        ];

        // Create some articles to return
        $articles = Article::factory()->count(10)->create();

        // Mock the repository's response
        $filters = [
            'categories' => [],
            'sources' => [],
            'authors' => [],
        ];

        $this->articleRepositoryMock
            ->shouldReceive('getArticles')
            ->once()
            ->with($filters, 15, 1)  // Pass the filters, per_page, and page
            ->andReturn($articles);

        // Call the service method
        $result = $this->articleService->fetchArticles($user->id, $queryParams);

        // Log and echo the result for inspection
        Log::debug("Result: ", ['result' => $result]);
        echo $result; // Optional - but use Log or print_r to inspect

        // Assert that the result is the expected articles
        $this->assertEquals($articles, $result);
    }


    public function test_fetch_articles_with_filters()
{
    // Create a user
    $user = User::factory()->create();

    // Define the filters to pass
    $queryParams = [
        'categories' => [1, 2],
        'sources' => [1],
        'authors' => [1],
        'per_page' => 15,
        'page' => 1,
    ];

    // Create the articles that will be returned by the mock
    $articles = Article::factory()->count(10)->make();

    // Define the filters that will actually be passed to the repository
    $filters = [
        'categories' => [1, 2], // From $queryParams
        'sources' => [1],        // From $queryParams
        'authors' => [1],        // From $queryParams
    ];

    // Mock the repository's response
    $this->articleRepositoryMock
        ->shouldReceive('getArticles')
        ->once()
        ->with($filters, 15, 1)  // Match the structure of the filters, per_page, and page
        ->andReturn($articles);

    // Call the service method
    $result = $this->articleService->fetchArticles($user->id, $queryParams);

    // Assert that the result is the expected articles
    $this->assertEquals($articles, $result);
}

}
