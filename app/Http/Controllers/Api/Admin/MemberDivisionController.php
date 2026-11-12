<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\Admin\Concerns\ProvidesCrudResponses;
use App\Http\Controllers\Controller;
use App\Models\MemberDivision;
use Illuminate\Http\Request;

class MemberDivisionController extends Controller
{
    use ProvidesCrudResponses;

    public function index(Request $request)
    {
        return $this->paginated($request, MemberDivision::class, orderByKey: true);
    }

    public function store(Request $request)
    {
        return response()->json([
            'data' => MemberDivision::create($request->validate(['name' => ['required', 'string', 'max:255']])),
        ], 201);
    }

    public function show(int $id)
    {
        return response()->json(['data' => $this->findRecord(MemberDivision::class, $id)]);
    }

    public function update(Request $request, int $id)
    {
        $division = MemberDivision::findOrFail($id);
        $division->update($request->validate(['name' => ['sometimes', 'required', 'string', 'max:255']]));

        return response()->json(['data' => $division->refresh()]);
    }

    public function destroy(int $id)
    {
        return $this->deleteRecord(MemberDivision::class, $id);
    }
}
