<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUploadLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('upload_log', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('type');
            $table->string('storage_path');
            $table->string('public_path');
            $table->unsignedBigInteger('uploader_id');
            $table->timestamps();

            $table->foreign('uploader_id')->references('id')->on('user')
                ->nullOnDelete()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('upload_log');
    }
}
