<?php

use Illuminate\Database\Seeder;

class SeedDistrictsTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $sql = database_path('factories/districts.sql');

        \DB::unprepared(file_get_contents($sql));
    }
}
