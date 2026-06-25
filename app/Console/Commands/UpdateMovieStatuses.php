<?php

namespace App\Console\Commands;

use App\Models\Movie;
use Illuminate\Console\Command;

class UpdateMovieStatuses extends Command
{
    protected $signature = 'movies:update-status';

    protected $description = 'Move movies between coming_soon -> now_showing -> archived based on release/end dates';

    public function handle(): int
    {
        Movie::syncStatuses();

        $this->info('Movie statuses updated based on release and end dates.');

        return self::SUCCESS;
    }
}