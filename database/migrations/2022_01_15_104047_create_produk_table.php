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
            $table->char('kode_sub_brand', 15)->unique();
            $table->string('nama_sub_brand');
            $table->timestamps();
        });

        Schema::create('brand', function (Blueprint $table) {
            $table->id();
            $table->char('kode_brand', 8)->unique();
            $table->string('nama_brand');
            $table->timestamps();
        });

        Schema::create('kategori', function (Blueprint $table) {
            $table->id();
            $table->char('kode_category', 8)->unique();
            $table->string('nama_category');
            $table->timestamps();
        });

        Schema::create('divisi', function (Blueprint $table) {
            $table->id();
            $table->char('kode_divisi', 8)->unique();
            $table->string('nama_divisi');
            $table->timestamps();
        });

        Schema::create('produk', function (Blueprint $table) {
            $table->id();
            $table->char('kode_produk', 8)->unique();
            $table->string('nama_produk');
            $table->char('kode_sub_brand', 15)->nullable();
            $table->string('kode_brand')->nullable();
            $table->string('kode_kategori')->nullable();
            $table->string('kode_divisi')->nullable();
            $table->timestamps();

            $table->foreign('kode_brand')->references('kode_brand')->on('brand')
                ->nullOnDelete()->onUpdate('cascade');
            $table->foreign('kode_sub_brand')->references('kode_sub_brand')->on('sub_brand')
                ->nullOnDelete()->onUpdate('cascade');
            $table->foreign('kode_kategori')->references('kode_category')->on('kategori')
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
