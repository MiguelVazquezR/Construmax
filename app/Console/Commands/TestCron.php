<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TestCron extends Command
{
    protected $signature = 'cron:test';

    protected $description = 'Test the execution of the scheduled job.';

    public function handle()
    {
        // Coloca aquí la lógica que deseas probar
        $this->info('Cron job test executed successfully!');
        Log::info('Cron teset executed');
    }
}
