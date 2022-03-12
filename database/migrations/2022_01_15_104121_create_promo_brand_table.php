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
            $table->bigInteger('budget_brand');
            $table->timestamps();

            $table->foreign('opso_id')->references('opso_id')->on('promo')
                ->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('kode_brand')->references('kode_brand')->on('brand')
                ->cascadeOnDelete()->cascadeOnUpdate();
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
