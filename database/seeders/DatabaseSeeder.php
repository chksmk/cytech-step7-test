<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
       

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        DB::table('companies')->insert([
            'id' => 1,
            'company_name' => 'thenewroad',
            'street_address' => '〒450-6425 愛知県名古屋市中村区名駅',
            'representative_name' => '千種',

            'id' => 2,
            'company_name' => '大名古屋ビル',
            'street_address' => '〒450-6425 愛知県名古屋市中村区名駅2',
            'representative_name' => '千草'
        ]);
    }
}