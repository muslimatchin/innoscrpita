<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\Author;
use App\Models\Category;
use App\Models\Source;
use App\Services\NewsApiService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FetchNewsArticles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-news-articles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch news articles from News API and store them in the database';

    protected $newsApiService;

    public function __construct(NewsApiService $newsApiService)
    {
        parent::__construct();
        $this->newsApiService = $newsApiService;
    }

    public function handle()
    {
        $this->newsApiService->fetchAndProcessArticles(function ($articleData) {
            $source = Source::firstOrCreate(
                ['name' => $articleData['source']['name']],
                ['url' => $articleData['url']]
            );

            $authorId = null;

                    $author = $articleData['author'] ?? null;
                    if($author)
                    {
                        $authorRow = Author::firstOrCreate(
                            ['name' => $author],
                        );
                        $authorId = $authorRow->id;
                    }

            $article =Article::updateOrCreate(
                ['url' => $articleData['url']],
                [
                    'source_id' => $source->id,
                    'author_id' => $authorId,
                    'title' => $articleData['title'],
                    'description' => $articleData['description'],
                    'content' => $articleData['content'],
                    'url_to_image' => $articleData['urlToImage'],
                    'published_at' => Carbon::parse($articleData['publishedAt']),
                    'provider' => 'News API',
                    'type' => 'article',
                    'category' => $articleData['category'],
                    'additional_data' => null,
                ]
            );

                     if (isset($articleData['category'])) {
                $categoryName = $articleData['category'];
                $category = Category::firstOrCreate(['name' => $categoryName]);
                $article->categories()->syncWithoutDetaching([$category->id]);
            }
        });

        $this->info('News articles fetched and stored successfully.');
    }
}
