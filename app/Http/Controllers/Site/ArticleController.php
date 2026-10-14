<?php

namespace App\Http\Controllers\Site;

use App\Data\Site\ArticleData;
use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function index(Request $request): View
    {
        $categories = ArticleData::categories();
        $category = in_array($request->query('category'), $categories, true) ? $request->query('category') : null;

        return view('site.article.index', [
            'articles' => collect(ArticleData::all())->when($category, fn ($c) => $c->where('category', $category))->values(),
            'categories' => $categories,
            'activeCategory' => $category,
        ]);
    }

    public function show(string $slug): View
    {
        $article = ArticleData::find($slug);
        abort_if(! $article, 404);

        return view('site.article.show', [
            'article' => $article,
            'package' => $article['related_package_slug'] ? Package::where('slug', $article['related_package_slug'])->first() : null,
            'related' => collect(ArticleData::all())->where('slug', '!=', $slug)->take(3)->values(),
        ]);
    }
}
