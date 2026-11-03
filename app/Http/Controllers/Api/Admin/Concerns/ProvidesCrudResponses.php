<?php

namespace App\Http\Controllers\Api\Admin\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

trait ProvidesCrudResponses
{
    protected function paginated(Request $request, string $modelClass, array $relations = [], bool $orderByKey = false)
    {
        $query = $modelClass::query();
        if ($relations) {
            $query->with($relations);
        }
        if ($orderByKey) {
            $query->orderBy((new $modelClass())->getKeyName(), 'desc');
        }

        return response()->json($query->paginate($request->integer('per_page', 20)));
    }

    protected function findRecord(string $modelClass, int $id, array $relations = []): Model
    {
        return $modelClass::query()->with($relations)->findOrFail($id);
    }

    protected function deleteRecord(string $modelClass, int $id)
    {
        $modelClass::query()->findOrFail($id)->delete();

        return response()->json(['message' => 'Resource deleted.']);
    }
}
