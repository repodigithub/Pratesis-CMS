<?php

use Database\Factories\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromoBrandTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promo_brand', function (Blueprint $table) {
            $table->id();
            $table->string('opso_id');
            $table->string('kode_brand');
            $table->string('nama_brand');
            $table->integer('budget_brand');
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
        Schema::dropIfExists('promo_brand');
    }
}
