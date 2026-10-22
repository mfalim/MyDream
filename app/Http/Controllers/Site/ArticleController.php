<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function index(Request $request): View
    {
        $categories = Article::published()->distinct()->orderBy('category')->pluck('category');
        $category = $categories->contains($request->query('category')) ? $request->query('category') : null;

        return view('site.article.index', [
            'articles' => Article::published()->when($category, fn ($q) => $q->where('category', $category))->latestFirst()->get(),
            'categories' => $categories,
            'activeCategory' => $category,
        ]);
    }

    public function show(string $slug): View
    {
        $article = Article::published()->with('package')->where('slug', $slug)->firstOrFail();

        return view('site.article.show', [
            'article' => $article,
            'package' => $article->package,
            'related' => Article::published()->where('id', '!=', $article->id)->latestFirst()->take(3)->get(),
        ]);
    }
}
