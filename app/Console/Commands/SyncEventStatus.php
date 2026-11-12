<?php

namespace App\Console\Commands;

use App\Models\Event;
use Illuminate\Console\Command;

class SyncEventStatus extends Command
{
    protected $signature = 'events:sync-status';

    protected $description = 'Automatically update event status based on scheduled time and status lifecycle';

    public function handle(): int
    {
        $updated = Event::syncAutomaticStatus();

        $this->info('Event status sync completed. Updated: ' . $updated);

        return self::SUCCESS;
    }
}
