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
        Schema::create('sub_brand', function (Blueprint $table) {
            $table->id();
            $table->string('kode_sub_brand')->unique();
            $table->string('nama_sub_brand');
            $table->timestamps();
        });

        Schema::create('brand', function (Blueprint $table) {
            $table->id();
            $table->string('kode_brand')->unique();
            $table->string('nama_brand');
            $table->timestamps();
        });

        Schema::create('kategori', function (Blueprint $table) {
            $table->id();
            $table->string('kode_kategori')->unique();
            $table->string('nama_kategori');
            $table->timestamps();
        });

        Schema::create('divisi', function (Blueprint $table) {
            $table->id();
            $table->string('kode_divisi')->unique();
            $table->string('nama_divisi');
            $table->timestamps();
        });

        Schema::create('produk', function (Blueprint $table) {
            $table->id();
            $table->string('kode_produk')->unique();
            $table->string('nama_produk');
            $table->string('kode_sub_brand')->nullable();
            $table->string('kode_brand')->nullable();
            $table->string('kode_kategori')->nullable();
            $table->string('kode_divisi')->nullable();
            $table->timestamps();

            $table->foreign('kode_brand')->references('kode_brand')->on('brand')
                ->nullOnDelete()->onUpdate('cascade');
            $table->foreign('kode_sub_brand')->references('kode_sub_brand')->on('sub_brand')
                ->nullOnDelete()->onUpdate('cascade');
            $table->foreign('kode_kategori')->references('kode_kategori')->on('kategori')
                ->nullOnDelete()->onUpdate('cascade');
            $table->foreign('kode_divisi')->references('kode_divisi')->on('divisi')
                ->nullOnDelete()->onUpdate('cascade');
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
        Schema::dropIfExists('divisi');
        Schema::dropIfExists('kategori');
        Schema::dropIfExists('sub_brand');
        Schema::dropIfExists('brand');
    }
}
