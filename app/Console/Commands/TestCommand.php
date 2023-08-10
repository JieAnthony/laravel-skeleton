<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Lottery;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'a:t';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $a = Lottery::odds(1, 10)->winner(function () {
            $this->info('?');
        })->choose();
        dump($a);

        return Command::SUCCESS;
    }
}
