<?php

use Database\Factories\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promo', function (Blueprint $table) {
            $table->id();
            $table->char('opso_id', 8)->unique();
            $table->string('nama_promo');
            $table->integer('budget_promo');
            $table->integer('budget_original');
            $table->integer('budget_update');
            $table->integer('selisih_budget');
            $table->integer('outstanding_claim');
            $table->integer('claim');
            $table->integer('sisa_budget');
            $table->char('kode_area', 6);
            $table->char('kode_distributor_group', 6);
            $table->char('kode_distributor', 8);
            $table->string('status_promo');
            $table->char('kode_spend_type');
            $table->date('tanggal_awal');
            $table->date('tanggal_ahir');
            $table->timestamps();

            $table->foreign('kode_area')->references('kode_area')->on('area')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('kode_distributor_group')->references('kode_distributor_group')->on('distributor_group')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('kode_distributor')->references('kode_distributor')->on('distributor')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('kode_spend_type')->references('kode_spend_type')->on('spend_type')
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
        Schema::dropIfExists('promo');
    }
}
