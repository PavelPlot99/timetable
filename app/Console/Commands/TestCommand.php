<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Timetable\ParseTimetable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tg:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     *
     */

    public function handle(): void
    {
        Log::info('asdasd');

    }
}
