<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFailedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('failed', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->json('contact')->nullable();
            $table->json('order')->nullable();
            $table->json('data')->nullable();
            $table->text('errors')->nullable();
            $table->string('organization_id')->nullable();
            $table->string('key');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('failed');
    }
}
