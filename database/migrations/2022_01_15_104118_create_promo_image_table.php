<?php

use Database\Factories\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromoImageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promo_image', function (Blueprint $table) {
            $table->id();
            $table->string('opso_id');
            $table->string('image_promo');
            $table->timestamps();
            
            $table->foreign('opso_id')->references('opso_id')->on('promo')
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
        Schema::dropIfExists('promo_image');
    }
}
