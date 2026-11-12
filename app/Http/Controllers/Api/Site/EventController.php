<?php

namespace App\Http\Controllers\Api\Site;

use App\Http\Controllers\Controller;
use App\Models\PublicEvent;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(PublicEvent::published()->when(
            $request->filled('q'),
            fn ($query) => $query->where('title', 'like', '%' . $request->query('q') . '%')
        )->orderBy('starts_on')->paginate($request->integer('per_page', 15)));
    }

    public function show(string $slug)
    {
        return response()->json(['data' => PublicEvent::published()->where('slug', $slug)->firstOrFail()]);
    }
}
