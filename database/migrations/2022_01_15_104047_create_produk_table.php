<?php

use Database\Factories\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProdukTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('produk', function (Blueprint $table) {
            $table->id();
            $table->char('kode_produk', 8)->unique();
            $table->string('nama_produk');
            $table->char('kode_sub_brand', 15);
            $table->string('category');
            $table->string('divisi');
            $table->timestamps();

            $table->foreign('kode_sub_brand')->references('kode_sub_brand')->on('sub_brand')
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
        Schema::dropIfExists('produk');
    }
}
