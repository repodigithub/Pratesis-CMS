<?php

use Database\Factories\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterClaimTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('claim', function (Blueprint $table) {
            $table->date('approved_date')->nullable();
            $table->string('bukti_bayar')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        $this->dropColumnIfExists('claim', 'bukti_bayar');
        $this->dropColumnIfExists('claim', 'approved_date');
    }
}
