<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class GuardianApiService
{
    protected $apiKey;
    protected $apiUrl;

    public function __construct()
    {
        $this->apiKey = config('services.guardian.key');
        $this->apiUrl = 'https://content.guardianapis.com/search';
    }

    public function fetchArticles($query = '', $fromDate = null, $toDate = null, $page = 1, $pageSize = 50)
    {
        $fromDate = $fromDate ?? Carbon::today()->toDateString();
        $toDate = $toDate ?? Carbon::today()->toDateString();

        $response = Http::get($this->apiUrl, [
            'api-key' => $this->apiKey,
            'q' => $query,
            'from-date' => $fromDate,
            'to-date' => $toDate,
            'page' => $page,
            'page-size' => $pageSize,
            'show-fields' => 'all',
        ]);

        if ($response->successful()) {
            return $response->json();
        } else {
            Log::error("Guardian API request failed with status: {$response->status()}");
            return null;
        }
    }
}
