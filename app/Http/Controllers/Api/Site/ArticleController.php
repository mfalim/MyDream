<?php

namespace App\Http\Controllers\Api\Site;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $query = Article::published()->latestFirst();
        if ($request->filled('category')) {
            $query->where('category', $request->query('category'));
        }

        return response()->json($query->paginate($request->integer('per_page', 15)));
    }

    public function show(string $slug)
    {
        return response()->json(['data' => Article::published()->where('slug', $slug)->firstOrFail()]);
    }
}
