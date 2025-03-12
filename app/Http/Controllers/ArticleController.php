<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApiResponse;
use App\Services\Interfaces\ArticleServiceInterface;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    //
    protected $articleService;

    public function __construct(ArticleServiceInterface $articleService)
    {
        $this->articleService = $articleService;
    }

    public function getArticles(Request $request)
    {
        $userId = $request->user()->id;
        $queryParams = $request->only(['categories', 'sources', 'authors','per_page', 'page']);

        $articles = $this->articleService->fetchArticles($userId, $queryParams);
        return new ApiResponse([
            "message" => "Articles retrieved",
            "data" => $articles
        ]);
    }
}
