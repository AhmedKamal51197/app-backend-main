<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\DummyDataSeeder;

class SeedDummyData extends Command
{
    protected $signature = 'seed:dummy';
    protected $description = 'Seed database with dummy data';

    public function handle()
    {
        $this->info('Seeding dummy data...');
        $this->call('db:seed', ['--class' => DummyDataSeeder::class]);
        $this->info('Dummy data seeded successfully!');
    }
}