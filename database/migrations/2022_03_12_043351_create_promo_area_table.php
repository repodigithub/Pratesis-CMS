<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromoAreaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promo_area', function (Blueprint $table) {
            $table->id();
            $table->string('opso_id');
            $table->string('kode_area');
            $table->string('budget');
            $table->timestamps();

            $table->foreign('opso_id')->references('opso_id')->on('promo')
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
        Schema::dropIfExists('promo_area');
    }
}
