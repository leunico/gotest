<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStarFieldToStarPackageUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('star_package_users', function (Blueprint $table) {
            $table->unsignedInteger('star')->default(0)->comment('星星数量');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('star_package_users', function (Blueprint $table) {
            $table->dropColumn('star');
        });
    }
}
