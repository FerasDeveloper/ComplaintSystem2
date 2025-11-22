<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\User_Goverment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    User::query()->create([
      'name' => 'feras',
      'email' => 'ferashatem@gmail.com',
      'password' => Hash::make('123123123'),
      'phone' => '9999999999',
      'is_verified' => 1,
      'role_id' => 1
    ]);

    User::query()->create([
      'name' => 'bshara',
      'email' => 'bsharahatem@gmail.com',
      'password' => Hash::make('123123123'),
      'phone' => '9999999998',
      'is_verified' => 1,
      'role_id' => 2
    ]);

    User::query()->create([
      'name' => 'sireen',
      'email' => 'sireen@gmail.com',
      'password' => Hash::make('123123123'),
      'phone' => '9999999997',
      'is_verified' => 1,
      'role_id' => 3
    ]);

    User::query()->create([
      'name' => 'test',
      'email' => 'test@gmail.com',
      'password' => Hash::make('123123123'),
      'phone' => '9999999996',
      'is_verified' => 1,
      'role_id' => 3
    ]);

    User::query()->create([
      'name' => 'siba',
      'email' => 'siba@gmail.com',
      'password' => Hash::make('123123123'),
      'phone' => '9999999995',
      'is_verified' => 1,
      'role_id' => 4
    ]);

    User::query()->create([
      'name' => 'hiba',
      'email' => 'hiba@gmail.com',
      'password' => Hash::make('123123123'),
      'phone' => '9999999994',
      'is_verified' => 1,
      'role_id' => 4
    ]);

    User_Goverment::query()->create([
      'user_id' => 2,
      'government_id' => 1
    ]);

    User_Goverment::query()->create([
      'user_id' => 3,
      'government_id' => 1
    ]);

    User_Goverment::query()->create([
      'user_id' => 4,
      'government_id' => 2
    ]);
  }
}
