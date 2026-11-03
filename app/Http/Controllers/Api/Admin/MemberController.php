<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\Admin\Concerns\ProvidesCrudResponses;
use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    use ProvidesCrudResponses;

    public function index(Request $request)
    {
        return $this->paginated($request, Member::class, orderByKey: true);
    }

    public function store(Request $request)
    {
        return response()->json(['data' => Member::create($this->validated($request))], 201);
    }

    public function show(int $id)
    {
        return response()->json(['data' => $this->findRecord(Member::class, $id)]);
    }

    public function update(Request $request, int $id)
    {
        $member = Member::findOrFail($id);
        $member->update($this->validated($request, true));

        return response()->json(['data' => $member->refresh()]);
    }

    public function destroy(int $id)
    {
        return $this->deleteRecord(Member::class, $id);
    }

    private function validated(Request $request, bool $partial = false): array
    {
        return $request->validate([
            'name' => [$partial ? 'sometimes' : 'required', 'string', 'max:255'],
            'user_id' => ['nullable', 'exists:users,id'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'domicile' => ['nullable', 'string'],
            'division' => [$partial ? 'sometimes' : 'required', 'string', 'max:255'],
            'position' => [$partial ? 'sometimes' : 'required', 'string', 'max:255'],
            'specialization' => ['nullable', 'string', 'max:255'],
            'certification' => ['nullable', 'string'],
            'ht_code' => ['nullable', 'string', 'max:255'],
            'daily_fee' => ['nullable', 'numeric', 'min:0'],
            'status' => ['sometimes', 'string', 'max:50'],
        ]);
    }
}
