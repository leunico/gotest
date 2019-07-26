<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameLoginLogColumnIpRegion extends Migration
{
    protected $table = 'login_logs';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable($this->table)) {
            Schema::table($this->table, function (Blueprint $table) {
                $table->renameColumn('ip_region', 'country');
                $table->renameColumn('province_id', 'province');
                $table->renameColumn('city_id', 'city');
                $table->renameColumn('district_id', 'district');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable($this->table)) {
            Schema::table($this->table, function (Blueprint $table) {
                $table->renameColumn('country', 'ip_region');
                $table->renameColumn('province', 'province_id');
                $table->renameColumn('city', 'city_id');
                $table->renameColumn('district', 'district_id');
            });
        }
    }
}
