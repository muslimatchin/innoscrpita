<?php

namespace Database\Seeders;


use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $categories = [
            ['name' => 'business'],
            ['name' => 'entertainment'],
            ['name' => 'general'],
            ['name' => 'health'],
            ['name' => 'science'],
            ['name' => 'sports'],
            ['name' => 'technology'],
        ];

        DB::table('categories')->insert($categories);
    }
}
