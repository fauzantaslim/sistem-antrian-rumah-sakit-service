<?php

namespace Database\Seeders;

use App\Models\Counter;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CounterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $counters = [
            [
                'counter_id' => (string) Str::ulid(),
                'counter_name' => 'Loket 1',
                'description' => 'Loket pelayanan umum',
            ],
            [
                'counter_id' => (string) Str::ulid(),
                'counter_name' => 'Loket 2',
                'description' => 'Loket pelayanan khusus',
            ],
            [
                'counter_id' => (string) Str::ulid(),
                'counter_name' => 'Loket 3',
                'description' => 'Loket informasi',
            ],
        ];

        foreach ($counters as $counter) {
            Counter::create($counter);
        }
    }
}
