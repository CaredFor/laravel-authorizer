<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFacilityIdToRolesAssignedIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('roles_assigned', function (Blueprint $table) {
            if (DB::getDriverName() !== 'sqlite') {
                $table->dropUnique(['role_id', 'entity_id', 'entity_type', 'team_id']);
            }
            $table->unique(['role_id', 'entity_id', 'entity_type', 'team_id', 'facility_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('roles_assigned', function (Blueprint $table) {
            if (DB::getDriverName() !== 'sqlite') {
                $table->dropUnique(['role_id', 'entity_id', 'entity_type', 'team_id', 'facility_id']);
            }
            $table->unique(['role_id', 'entity_id', 'entity_type', 'team_id']);
        });
    }
}
