<?php

namespace Database\Seeders;

use App\Models\ComplaintType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ComplaintTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ComplaintType::query()->create([
          'name' => 'Services'
        ]);

        ComplaintType::query()->create([
          'name' => 'Employee'
        ]);

        ComplaintType::query()->create([
          'name' => 'Other'
        ]);
    }
}
