<?php

use Database\Factories\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupPermissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group_permission', function (Blueprint $table) {
            $table->id();
            $table->char('kode_group');
            $table->char('kode_permission');
            $table->timestamps();

            $table->foreign('kode_group')->references('kode_group')->on('user_group')
                ->cascadeOnUpdate()->cascadeOnUpdate();
            $table->foreign('kode_permission')->references('kode_permission')->on('permission')
                ->cascadeOnUpdate()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('group_permission');
    }
}
