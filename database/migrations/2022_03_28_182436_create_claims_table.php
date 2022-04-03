<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClaimsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('claim', function (Blueprint $table) {
            $table->id();
            $table->string('kode_uli');
            $table->unsignedBigInteger('promo_distributor_id');
            $table->string('status');
            $table->bigInteger('amount');
            $table->string('laporan_tpr_barang')->nullable();
            $table->string('laporan_tpr_uang')->nullable();
            $table->string('faktur_pajak')->nullable();
            $table->text('description')->nullable();
            $table->text('alasan')->nullable();
            $table->timestamps();

            $table->foreign('promo_distributor_id')->references('id')->on('promo_distributor')
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
        Schema::dropIfExists('claims');
    }
}
