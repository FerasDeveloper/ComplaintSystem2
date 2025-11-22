<?php

namespace Database\Seeders;

use App\Models\Government;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GovernmentSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    Government::query()->create([
      'name' => 'Ministry of Electricity',
      'location' => 'Mazza',
      'description' => 'des1'
    ]);

    Government::query()->create([
      'name' => 'Ministry of Education',
      'location' => 'Mazza',
      'description' => 'des2'
    ]);

    Government::query()->create([
      'name' => 'Ministry of Environment',
      'location' => 'Mazza',
      'description' => 'des3'
    ]);

    Government::query()->create([
      'name' => 'Ministry of Justic',
      'location' => 'Mazza',
      'description' => 'des4'
    ]);
  }
}
