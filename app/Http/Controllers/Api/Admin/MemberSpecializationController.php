<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\Admin\Concerns\ProvidesCrudResponses;
use App\Http\Controllers\Controller;
use App\Models\MemberSpecialization;
use Illuminate\Http\Request;

class MemberSpecializationController extends Controller
{
    use ProvidesCrudResponses;

    public function index(Request $request)
    {
        return $this->paginated($request, MemberSpecialization::class, orderByKey: true);
    }

    public function store(Request $request)
    {
        return response()->json([
            'data' => MemberSpecialization::create($request->validate(['name' => ['required', 'string', 'max:255']])),
        ], 201);
    }

    public function show(int $id)
    {
        return response()->json(['data' => $this->findRecord(MemberSpecialization::class, $id)]);
    }

    public function update(Request $request, int $id)
    {
        $specialization = MemberSpecialization::findOrFail($id);
        $specialization->update($request->validate(['name' => ['sometimes', 'required', 'string', 'max:255']]));

        return response()->json(['data' => $specialization->refresh()]);
    }

    public function destroy(int $id)
    {
        return $this->deleteRecord(MemberSpecialization::class, $id);
    }
}
