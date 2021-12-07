<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRolesAssignedLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles_assigned_log', function (Blueprint $table) {
            $table->uuid('id');
            $table->uuid('role_id');
            $table->uuid('entity_id');
            $table->string('entity_type');
            $table->uuid('team_id')->nullable();
            $table->uuid('facility_id')->nullable();
            $table->timestamp('role_assigned_at');
            $table->timestamp('role_removed_at')->nullable();

            $table->index(['entity_id', 'entity_type']);
            $table->index('team_id');
            $table->index('facility_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('roles_assigned_log');
    }
}
