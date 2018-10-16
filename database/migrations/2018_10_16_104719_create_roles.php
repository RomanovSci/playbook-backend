<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

class CreateRoles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $roles = [
            User::ROLE_USER,
            User::ROLE_COACH,
            User::ROLE_PLACE_ADMIN,
            User::ROLE_ADMIN,
        ];

        foreach ($roles as $role) {
            Role::create(['name' => $role]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('roles')->delete();
    }
}
