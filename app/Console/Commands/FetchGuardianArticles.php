<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\Author;
use App\Models\Category;
use App\Models\Source;
use App\Services\GuardianApiService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FetchGuardianArticles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-guardian-articles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch and store today\'s articles from The Guardian API';

    protected $guardianApiService;

    public function __construct(GuardianApiService $guardianApiService)
    {
        parent::__construct();
        $this->guardianApiService = $guardianApiService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $query = '';
        $page = 1;
        $pageSize = 50;
        $totalPages = 1;

        while ($page <= $totalPages) {
            $response = $this->guardianApiService->fetchArticles($query, null, null, $page, $pageSize);

            Log::info($response);
            if ($response && isset($response['response']['results'])) {
                $articles = $response['response']['results'];
                $totalPages = $response['response']['pages'];

                foreach ($articles as $articleData) {
                    if ( strlen( $articleData['webUrl']) > 1000) { // As it exceeds the length sometimes
                        Log::warning("Skipped article due to 'url_to_image' length");
                        continue; // Skip this record
                    }
                    $source = Source::firstOrCreate(
                        ['name' => 'guardian'],
                    );

                    $authorId = null;

                    $author = $articleData['fields']['byline'] ?? null;
                    if($author)
                    {
                        $authorRow = Author::firstOrCreate(
                            ['name' => $author],
                        );
                        $authorId = $authorRow->id;
                    }

                    $article  = Article::updateOrCreate(
                        ['url' => $articleData['webUrl']],
                        [
                            'source_id' => $source->id,
                            'author_id' => $authorId ,
                            'title' => $articleData['webTitle'],
                            'description' => $articleData['fields']['trailText'] ?? '',
                            'content' => $articleData['fields']['body'] ?? '',
                            'url_to_image' => $articleData['fields']['thumbnail'] ?? '',
                            'published_at' => Carbon::parse($articleData['webPublicationDate']),
                            'provider' => 'The Guardian',
                            'type' => 'article',
                            'additional_data' => null,
                        ]
                    );

                    if(isset($articleData['sectionName']))
                    {
                        $category = Category::firstOrCreate(['name' => $articleData['sectionName']]);
                        $article->categories()->syncWithoutDetaching([$category->id]);
                    }
                }

                $this->info("Page {$page} of {$totalPages} processed.");
                $page++;
            } else {
                $this->error('Failed to fetch articles.');
                break;
            }
        }

        $this->info('Article fetching completed.');
    }
}
