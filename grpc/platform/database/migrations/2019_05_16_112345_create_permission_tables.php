<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Spatie\Permission\Models\Role;

class CreatePermissionTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableNames = config('permission.table_names');
        $columnNames = config('permission.column_names');

        Schema::create($tableNames['permissions'], function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('guard_name');
            $table->string('title')->nullable();
            $table->string('description')->nullable();
            $table->string('category', 50)->nullable();
            $table->timestamps();
        });

        Schema::create($tableNames['roles'], function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('guard_name');
            $table->string('title')->nullable();
            $table->string('description')->nullable();
            $table->unsignedInteger('creator_id')->default(0);
            $table->timestamps();
        });

        Schema::create($tableNames['model_has_permissions'], function (Blueprint $table) use ($tableNames, $columnNames) {
            $table->unsignedInteger('permission_id');

            $table->string('model_type');
            $table->unsignedBigInteger($columnNames['model_morph_key']);
            $table->index([$columnNames['model_morph_key'], 'model_type', ]);
            $table->index('permission_id');

            // $table->foreign('permission_id')
            //     ->references('id')
            //     ->on($tableNames['permissions'])
            //     ->onDelete('cascade');

            $table->primary(['permission_id', $columnNames['model_morph_key'], 'model_type'],
                    'model_has_permissions_permission_model_type_primary');
        });

        Schema::create($tableNames['model_has_roles'], function (Blueprint $table) use ($tableNames, $columnNames) {
            $table->unsignedInteger('role_id');

            $table->string('model_type');
            $table->unsignedBigInteger($columnNames['model_morph_key']);
            $table->index([$columnNames['model_morph_key'], 'model_type', ]);
            $table->index('role_id');

            // $table->foreign('role_id')
            //     ->references('id')
            //     ->on($tableNames['roles'])
            //     ->onDelete('cascade');

            $table->primary(['role_id', $columnNames['model_morph_key'], 'model_type'],
                    'model_has_roles_role_model_type_primary');
        });

        Schema::create($tableNames['role_has_permissions'], function (Blueprint $table) use ($tableNames) {
            $table->unsignedInteger('permission_id');
            $table->unsignedInteger('role_id');

            // $table->foreign('permission_id')
            //     ->references('id')
            //     ->on($tableNames['permissions'])
            //     ->onDelete('cascade');

            // $table->foreign('role_id')
            //     ->references('id')
            //     ->on($tableNames['roles'])
            //     ->onDelete('cascade');

            $table->index('permission_id');
            $table->index('role_id');
            $table->primary(['permission_id', 'role_id']);
        });

        DB::table('users')->insert(['name' => 'admin', 'password' => bcrypt('123456'), 'email' => 'codepku@codepku.com', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        Role::create(['name' => 'admin', 'guard_name' => 'api', 'title' => '后台管理员', 'description' => '最高权限']);

        app('cache')
            ->store(config('permission.cache.store') != 'default' ? config('permission.cache.store') : null)
            ->forget(config('permission.cache.key'));
        app('cache')->forget('spatie.permission.cache');

        App\Models\User::where('name', 'admin')->orderBy('id', 'desc')->first()->assignRole('admin');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tableNames = config('permission.table_names');

        Schema::drop($tableNames['role_has_permissions']);
        Schema::drop($tableNames['model_has_roles']);
        Schema::drop($tableNames['model_has_permissions']);
        Schema::drop($tableNames['roles']);
        Schema::drop($tableNames['permissions']);
    }
}
