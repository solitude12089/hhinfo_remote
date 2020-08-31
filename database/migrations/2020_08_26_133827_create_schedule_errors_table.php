<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScheduleErrorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedule_errors', function (Blueprint $table) {
            $table->increments('id');
            $table->string('ip');
            $table->string('path');
            $table->integer('status')->default(1);
            $table->text('errorMsg')->nullable();
            $table->integer('rty')->default(0);
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
        Schema::dropIfExists('schedule_errors');
    }
}
