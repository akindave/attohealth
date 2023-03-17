<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UserCategory;

class UserCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {
        //
        UserCategory::create([
            'name' => 'Employer'
        ]);

        UserCategory::create([
            'name' => 'Employee'
        ]);
    }
}
