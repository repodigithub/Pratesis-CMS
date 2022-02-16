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
            $table->char('kode_kegiatan')->unique();
            $table->string('nama_kegiatan');
            $table->text('deskripsi_kegiatan');
            $table->char('kode_ppn')->nullable();
            $table->char('kode_pph')->nullable();
            $table->char('kode_investment', 4)->nullable();
            $table->string('file_dokumen')->nullable();
            $table->timestamps();

            $table->foreign('kode_investment')->references('kode_investment')->on('investment')
                ->nullOnDelete()->cascadeOnUpdate();
            $table->foreign('kode_ppn')->references('kode_pajak')->on('tax')
                ->nullOnDelete()->cascadeOnUpdate();
            $table->foreign('kode_pph')->references('kode_pajak')->on('tax')
                ->nullOnDelete()->cascadeOnUpdate();
        });

        Schema::create('tipe_promo_dokumen_klaim', function (Blueprint $table) {
            $table->char('kode_kegiatan');
            $table->char('kode_dokumen');
            $table->timestamps();

            $table->primary(['kode_kegiatan', 'kode_dokumen']);
            $table->foreign('kode_kegiatan')->references('kode_kegiatan')->on('tipe_promo')
                ->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('kode_dokumen')->references('kode_dokumen')->on('dokumen_klaim')
                ->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::create('tipe_promo_spend_type', function (Blueprint $table) {
            $table->char('kode_kegiatan');
            $table->char('kode_spend_type');
            $table->timestamps();

            $table->primary(['kode_kegiatan', 'kode_spend_type']);
            $table->foreign('kode_kegiatan')->references('kode_kegiatan')->on('tipe_promo')
                ->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('kode_spend_type')->references('kode_spend_type')->on('spend_type')
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
        Schema::dropIfExists('tipe_promo_dokumen_klaim');
        Schema::dropIfExists('tipe_promo_spend_type');
        Schema::dropIfExists('tipe_promo');
    }
}
