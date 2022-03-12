<?php

use Database\Factories\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDistributorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('distributor', function (Blueprint $table) {
            $table->id();
            $table->string('kode_distributor')->unique();
            $table->string('nama_distributor');
            $table->string('kode_distributor_group');
            $table->string('kode_area');
            $table->string('alamat')->nullable();
            $table->string('titik_koordinat')->nullable();
            $table->string('status_distributor');
            $table->timestamps();

            $table->foreign('kode_distributor_group')->references('kode_distributor_group')->on('distributor_group')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('kode_area')->references('kode_area')->on('area')
                ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('distributor');
    }
}
