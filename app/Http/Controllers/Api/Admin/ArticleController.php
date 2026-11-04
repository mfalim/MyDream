<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\Admin\Concerns\ProvidesCrudResponses;
use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    use ProvidesCrudResponses;

    public function index(Request $request)
    {
        return $this->paginated($request, Article::class, orderByKey: true);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $article = new Article();
        $article->fill($data);
        if (blank($article->slug)) {
            $article->slug = Str::slug($article->title);
        }
        $article->save();

        return response()->json(['data' => $article], 201);
    }

    public function show(int $id)
    {
        return response()->json(['data' => $this->findRecord(Article::class, $id)]);
    }

    public function update(Request $request, int $id)
    {
        $article = Article::findOrFail($id);
        $article->fill($this->validated($request, $article, true))->save();

        return response()->json(['data' => $article->refresh()]);
    }

    public function destroy(int $id)
    {
        return $this->deleteRecord(Article::class, $id);
    }

    private function validated(Request $request, ?Article $article = null, bool $partial = false): array
    {
        $data = $request->validate([
            'title' => [$partial ? 'sometimes' : 'required', 'string', 'max:255'],
            'slug' => ['sometimes', 'nullable', 'string', 'max:255', 'unique:articles,slug' . ($article ? ',' . $article->getKey() : '')],
            'category' => ['sometimes', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string'],
            'content' => [$partial ? 'sometimes' : 'required', 'string'],
            'author' => ['nullable', 'string', 'max:255'],
            'published_at' => ['nullable', 'date'],
            'package_id' => ['nullable', 'exists:packages,id'],
            'is_published' => ['sometimes', 'boolean'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('articles', 'public');
        }

        return $data;
    }
}
