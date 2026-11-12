<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\Admin\Concerns\ProvidesCrudResponses;
use App\Http\Controllers\Controller;
use App\Models\MemberPosition;
use Illuminate\Http\Request;

class MemberPositionController extends Controller
{
    use ProvidesCrudResponses;

    public function index(Request $request)
    {
        return $this->paginated($request, MemberPosition::class, orderByKey: true);
    }

    public function store(Request $request)
    {
        return response()->json([
            'data' => MemberPosition::create($request->validate(['name' => ['required', 'string', 'max:255']])),
        ], 201);
    }

    public function show(int $id)
    {
        return response()->json(['data' => $this->findRecord(MemberPosition::class, $id)]);
    }

    public function update(Request $request, int $id)
    {
        $position = MemberPosition::findOrFail($id);
        $position->update($request->validate(['name' => ['sometimes', 'required', 'string', 'max:255']]));

        return response()->json(['data' => $position->refresh()]);
    }

    public function destroy(int $id)
    {
        return $this->deleteRecord(MemberPosition::class, $id);
    }
}
