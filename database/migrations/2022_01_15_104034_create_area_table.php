<?php

use Database\Factories\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAreaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('area', function (Blueprint $table) {
            $table->id();
            $table->string('kode_area')->unique();
            $table->string('nama_area');
            $table->text('alamat_depo');
            $table->string('kode_region');
            $table->string('titik_koordinat')->nullable();
            $table->timestamps();

            $table->foreign('kode_region')->references('kode_region')->on('region')
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
        Schema::dropIfExists('area');
    }
}
