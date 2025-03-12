<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NewsApiService
{
    protected $apiKey;
    protected $categories;

    public function __construct()
    {
        $this->apiKey = config('services.newsapi.key');
        $this->categories = ['business', 'entertainment', 'general', 'health', 'science', 'sports', 'technology'];

    }

    public function fetchAndProcessArticles(callable $processArticle)
    {
        foreach ($this->categories as $category) {
            $response = Http::get('https://newsapi.org/v2/top-headlines', [
                'category' => $category,
                'apiKey' => $this->apiKey,
            ]);

            if ($response->successful()) {
                $articles = $response->json()['articles'];
                foreach ($articles as $articleData) {
                    $articleData['category'] = $category; // Add category to each article
                    $processArticle($articleData);
                }
            } else {
                Log::warning("Request for category '{$category}' failed with status: {$response->status()}");
            }
        }
    }

//     public function fetchArticles()
// {
//     $categories = ['business', 'entertainment', 'general', 'health', 'science', 'sports', 'technology'];

//     $responses = Http::pool(function ($pool) use ($categories) {
//         foreach ($categories as $category) {
//             $pool->as($category)->get('https://newsapi.org/v2/top-headlines', [
//                 'category' => $category,
//                 'apiKey' => $this->apiKey,
//             ]);
//         }
//     });

//     $articles = [];

//     foreach ($categories as $category) {
//         $response = $responses[$category];

//         if ($response instanceof Response) {
//             if ($response->successful()) {
//                 $fetchedArticles = $response->json()['articles'];
//                 foreach ($fetchedArticles as &$article) {
//                     $article['category'] = $category; // Add category to each article
//                 }
//                 $articles = array_merge($articles, $fetchedArticles);
//             } else {
//                 // Log the unsuccessful response status
//                 Log::warning("Request for category '{$category}' failed with status: {$response->status()}");
//             }
//         } else {
//             // Log the exception message
//             Log::error("Request for category '{$category}' encountered an error: {$response->getMessage()}");
//         }
//     }

//     return $articles;
// }
}
