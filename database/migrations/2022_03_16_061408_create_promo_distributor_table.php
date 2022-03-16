<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromoDistributorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promo_distributor', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('promo_area_id');
            $table->string('kode_distributor');
            $table->bigInteger('budget');
            $table->string('status')->nullable();
            $table->timestamps();

            $table->foreign('promo_area_id')->references('id')->on('promo_area')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('kode_distributor')->references('kode_distributor')->on('distributor')
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
        Schema::dropIfExists('promo_distributor');
    }
}
