<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'month' => ['sometimes', 'date_format:Y-m'],
            'date' => ['sometimes', 'date'],
        ]);
        $query = Event::with(['booking.client', 'package'])
            ->when(isset($filters['date']), fn ($q) => $q->whereDate('event_date', $filters['date']))
            ->when(isset($filters['month']), fn ($q) => $q->whereYear('event_date', substr($filters['month'], 0, 4))->whereMonth('event_date', substr($filters['month'], 5, 2)))
            ->orderBy('event_date');

        return response()->json(['data' => $query->get()]);
    }
}
