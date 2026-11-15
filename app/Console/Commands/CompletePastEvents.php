<?php

namespace App\Console\Commands;

use App\Models\Event;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CompletePastEvents extends Command
{
    protected $signature = 'events:complete-past';
    protected $description = 'Tandai acara lampau dari booking approved sebagai completed';

    public function handle(): int
    {
        $updated = Event::whereIn('status', ['scheduled', 'ongoing'])
            ->whereDate('event_date', '<', today())
            ->whereHas('booking', fn ($query) => $query->where('status', 'approved'))
            ->update(['status' => 'completed', 'updated_at' => now()]);

        $this->info("{$updated} acara ditandai selesai.");

        return self::SUCCESS;
    }
}
