<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        DB::table('admin')->insert(
            ["id" => "1","login" => "admin","password" => '$2y$10$eYxRUgU2XiJH3MN86XfTweKFmL3HJDuu2vhSnZ7D61TkgJDV7QIsq',"name" => "Administrator","language" => "us","roles_id" => "1"]
        );
    }
}
