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
            $table->string('opso_id')->unique();
            $table->string('nama_promo');
            $table->bigInteger('budget');
            $table->string('status');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('claim');
            $table->string('kode_spend_type')->nullable();
            $table->string('kode_budget_holder')->nullable();
            $table->string('file')->nullable();
            $table->timestamps();

            $table->foreign('kode_spend_type')->references('kode_spend_type')->on('spend_type')
                ->nullOnDelete()->cascadeOnUpdate();
            $table->foreign('kode_budget_holder')->references('kode_budget_holder')->on('budget_holder')
                ->nullOnDelete()->cascadeOnUpdate();
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
