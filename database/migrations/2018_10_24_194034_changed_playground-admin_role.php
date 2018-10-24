<?php

use Spatie\Permission\Models\Role;
use Illuminate\Database\Migrations\Migration;

class ChangedPlaygroundAdminRole extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Role::where('name', 'playground-admin')->delete();
        Role::create(['name' => 'organization-admin']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Role::where('name', 'organization-admin')->delete();
        Role::create(['name' => 'playground-admin']);
    }
}
