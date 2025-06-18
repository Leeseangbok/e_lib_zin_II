<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GutendexService; // We will reuse our service
use App\Console\Commands\FetchBookContentCommand; // Import the new command

class Kernel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:kernel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     *
     */
    // In app/Console/Kernel.php
    protected $commands = [
        FetchBooksCommand::class,          // This one already exists
        FetchBookContentCommand::class,    // Add this new line
    ];
    public function handle()
    {
        //
    }
}
