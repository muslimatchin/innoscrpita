<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApiResponse;
use App\Services\Interfaces\PreferenceServiceInterface;
use Illuminate\Http\Request;

class PreferenceController extends Controller
{
    //
    protected $preferenceService;

    public function __construct(PreferenceServiceInterface $preferenceService)
    {
        $this->preferenceService = $preferenceService;
    }

    public function getPreferences(Request $request)
    {
        $preferences = $this->preferenceService->getUserPreferences($request->user()->id);
        return new ApiResponse([
            "message" => "preference fetched",
            "data" => $preferences
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sources' => 'array|exists:sources,id',
            'categories' => 'array|exists:categories,id',
            'authors' => 'array|exists:authors,id',
        ]);

        $preferences = $this->preferenceService->setUserPreferences($request->user()->id, $validated);
        return new ApiResponse([
            'message' => 'Preferences updated successfully.',
            "data" => $preferences
        ]);
    }
}
