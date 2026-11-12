<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\Admin\Concerns\ProvidesCrudResponses;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use ProvidesCrudResponses;

    public function index(Request $request)
    {
        return $this->paginated($request, Category::class, orderByKey: true);
    }

    public function store(Request $request)
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:255']]);

        return response()->json(['data' => Category::create($data)], 201);
    }

    public function show(int $id)
    {
        return response()->json(['data' => $this->findRecord(Category::class, $id)]);
    }

    public function update(Request $request, int $id)
    {
        $category = Category::findOrFail($id);
        $category->update($request->validate(['name' => ['sometimes', 'required', 'string', 'max:255']]));

        return response()->json(['data' => $category->refresh()]);
    }

    public function destroy(int $id)
    {
        return $this->deleteRecord(Category::class, $id);
    }
}
