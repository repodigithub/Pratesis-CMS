<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpendTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('spend_type', function (Blueprint $table) {
            $table->id();
            $table->char('kode_spend_type')->unique();
            $table->char('kode_investment', 4);
            $table->integer('fund_type');
            $table->char('reference_tax');
            $table->char('condition_type');
            $table->timestamps();

            $table->foreign('kode_investment')->references('kode_investment')->on('investment')
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
        Schema::dropIfExists('spend_type');
    }
}
