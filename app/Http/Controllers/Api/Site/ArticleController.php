<?php

namespace App\Http\Controllers\Api\Site;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $query = Article::published()->with(['package.vendors.photos', 'event'])->latestFirst();
        if ($request->filled('category')) {
            $query->where('category', $request->query('category'));
        }

        return response()->json($query->paginate($request->integer('per_page', 15)));
    }

    public function show(string $slug)
    {
        $article = Article::published()
            ->with(['package.vendors.photos', 'event'])
            ->where(function ($query) use ($slug) {
                $query->where('slug', $slug);
                if (is_numeric($slug)) {
                    $query->orWhere('id', (int) $slug);
                }
            })
            ->firstOrFail();

        $related = Article::published()
            ->with(['package.vendors.photos', 'event'])
            ->where('id', '!=', $article->id)
            ->latestFirst()
            ->take(3)
            ->get();

        $article->setAttribute('related', $related);

        return response()->json(['data' => $article]);
    }
}
