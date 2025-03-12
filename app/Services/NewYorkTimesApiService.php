<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NewYorkTimesApiService
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.nyt.key');
        $this->baseUrl = 'https://api.nytimes.com/svc/search/v2/articlesearch.json';
    }

    public function fetchArticles($query = '', $date, $page = 0)
    {
        Log::info("nytapi key".$this->apiKey);
        $params = [
            'q' => $query,
            'api-key' => $this->apiKey,
            'page' => $page,
            'begin_date' => $date->format('Ymd'),
            'end_date' => $date->format('Ymd'),
        ];

        try {
            $response = Http::get($this->baseUrl, $params);

            if ($response->successful()) {
                return $response->json();
            } else {
                Log::error('NYT API Error: ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('HTTP Request Exception: ' . $e->getMessage());
        }

        return null;
    }
}
