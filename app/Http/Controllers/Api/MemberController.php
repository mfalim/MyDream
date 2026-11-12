<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MemberController extends Controller
{
    private const STATUSES = ['standby', 'aktif', 'resiko', 'selesai'];

    public function index(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'q' => ['sometimes', 'string', 'max:100'],
            'division' => ['sometimes', 'string', 'max:100'],
            'status' => ['sometimes', 'string', 'in:' . implode(',', self::STATUSES)],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ]);

        $members = Member::query()
            ->select([
                'id',
                'member_code',
                'name',
                'call_sign',
                'division',
                'position',
                'specialization',
                'photo',
                'status',
            ])
            ->when(isset($filters['q']), function ($query) use ($filters) {
                $search = $filters['q'];

                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('member_code', 'like', "%{$search}%")
                        ->orWhere('call_sign', 'like', "%{$search}%");
                });
            })
            ->when(isset($filters['division']), fn ($query) => $query->where('division', $filters['division']))
            ->when(isset($filters['status']), fn ($query) => $query->where('status', $filters['status']))
            ->orderBy('name')
            ->paginate($filters['per_page'] ?? 15)
            ->through(fn (Member $member) => $this->memberData($member));

        return response()->json($members);
    }

    public function show(Member $member): JsonResponse
    {
        return response()->json([
            'data' => $this->memberData($member),
        ]);
    }

    private function memberData(Member $member): array
    {
        return [
            'id' => $member->id,
            'member_code' => $member->member_code,
            'name' => $member->name,
            'call_sign' => $member->call_sign,
            'division' => $member->division,
            'position' => $member->position,
            'specialization' => $member->specialization,
            'photo_url' => $member->photo ? Storage::disk('public')->url($member->photo) : null,
            'status' => $member->status,
        ];
    }
}
