<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\Author;
use App\Models\Category;
use App\Models\Source;
use App\Services\NewYorkTimesApiService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FetchNewYorkTimesArticles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-new-york-times-articles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $nytApiService;

    public function __construct(NewYorkTimesApiService $nytApiService)
    {
        parent::__construct();
        $this->nytApiService = $nytApiService;
    }
    public function handle()
    {
        $query = '';
        $page = 0;
        $date = Carbon::today();

        do {
            $response = $this->nytApiService->fetchArticles($query, $date, $page);

            if ($response && isset($response['response']['docs'])) {
                $articles = $response['response']['docs'];
                $totalPages = ceil($response['response']['meta']['hits'] / 10); // NYT API returns 10 articles per page

                foreach ($articles as $articleData) {
                    $url = $articleData['web_url'];
                    $urlToImage = $articleData['multimedia'][0]['url'] ?? '';

                    if (!$this->isValidUrlLength($url)) {
                        Log::warning("Skipped article due to 'url' length");
                        continue;
                    }

                    if (!$this->isValidUrlLength($urlToImage)) {
                        Log::warning("Skipped article due to 'url_to_image' length");
                        continue;
                    }

                    $source = Source::firstOrCreate(['name' => 'The New York Times']);

                    $authorId = null;

                    Log::info("authorid is $authorId");

                    $author = $articleData['byline']['original'] ?? null;
                    if($author)
                    {
                        $authorRow = Author::firstOrCreate(
                            ['name' => $author],
                        );
                        Log::info($authorRow );
                        $authorId = $authorRow->id;
                        Log::info("authorid 2 is $authorId");
                    }

                    Log::info("authorid 3 is $authorId");

                    $article = Article::updateOrCreate(
                        ['url' => $url],
                        [
                            'source_id' => $source->id,
                            'author_id' => $authorId,
                            'title' => $articleData['headline']['main'],
                            'description' => $articleData['abstract'] ?? '',
                            'content' => $articleData['lead_paragraph'] ?? '',
                            'url_to_image' => $urlToImage,
                            'published_at' => Carbon::parse($articleData['pub_date']),
                            'provider' => 'The New York Times',
                            'type' => 'article',
                            'additional_data' => null,
                        ]
                    );

                    if (isset($articleData['section_name'])) {
                        $category = Category::firstOrCreate(['name' => $articleData['section_name']]);
                        $article->categories()->syncWithoutDetaching([$category->id]);
                    }
                }

                $this->info("Page {$page} of {$totalPages} processed.");
                $page++;
            } else {
                $this->error('Failed to fetch articles.');
                break;
            }
        } while ($page < $totalPages);

        $this->info('Article fetching completed.');
    }

    public function isValidUrlLength($url)
    {
        return strlen($url) <= 1000;
    }
}
