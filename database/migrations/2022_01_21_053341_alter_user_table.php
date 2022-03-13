<?php

use Database\Factories\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('user', function (Blueprint $table) {
            $table->string('user_id')->unique();
            $table->string('full_name');
            $table->string('email')->unique();
            $table->string('username')->unique();
            $table->string('password');
            $table->string('status')->default('waiting_approval');
            $table->string('kode_distributor')->nullable();
            $table->string('kode_area')->nullable();

            $table->foreign('kode_distributor')->references('kode_distributor')->on('distributor')->nullOnDelete();
            $table->foreign('kode_area')->references('kode_area')->on('area')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropColumnIfExists('user', 'user_id');
        $this->dropColumnIfExists('user', 'full_name');
        $this->dropColumnIfExists('user', 'email');
        $this->dropColumnIfExists('user', 'username');
        $this->dropColumnIfExists('user', 'password');
        $this->dropColumnIfExists('user', 'status');
        $this->dropColumnIfExists('user', 'user_level');
        $this->dropColumnIfExists('user', 'kode_distributor');
        $this->dropColumnIfExists('user', 'kode_area');
    }
}
