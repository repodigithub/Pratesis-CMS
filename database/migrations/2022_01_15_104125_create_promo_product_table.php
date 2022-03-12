<?php

use Database\Factories\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromoProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promo_product', function (Blueprint $table) {
            $table->id();
            $table->string('opso_id');
            $table->string('kode_brand');
            $table->string('nama_brand');
            $table->string('kode_produk');
            $table->integer('budget_produk');
            $table->timestamps();

            $table->foreign('opso_id')->references('opso_id')->on('promo')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('kode_produk')->references('kode_produk')->on('produk')
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
        Schema::dropIfExists('promo_product');
    }
}
