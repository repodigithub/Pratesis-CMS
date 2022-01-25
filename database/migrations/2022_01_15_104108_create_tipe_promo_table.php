<?php

use Database\Factories\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTipePromoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipe_promo', function (Blueprint $table) {
            $table->id();
            $table->char('kode_tipe_promo')->unique();
            $table->string('nama_kegiatan');
            $table->text('deskripsi_kegiatan');
            $table->char('kode_spend_type');
            $table->char('kode_investment', 4);
            $table->char('kode_ppn');
            $table->char('kode_dokumen', 4);
            $table->string('file_dokumen');
            $table->timestamps();

            $table->foreign('kode_spend_type')->references('kode_spend_type')->on('spend_type')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('kode_investment')->references('kode_investment')->on('investment')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('kode_ppn')->references('kode_pajak')->on('tax')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('kode_dokumen')->references('kode_dokumen')->on('dokumen_klaim')
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
        Schema::dropIfExists('tipe_promo');
    }
}
