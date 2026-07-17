<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestCBF extends Command
{
    protected $signature = 'test:cbf';

    protected $description = 'Menguji perhitungan Content-Based Filtering';

    public function handle()
    {
        $this->info('Command Test CBF berhasil dijalankan!');
    }
}
    