<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDevicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->increments('id');
            $table->string('ip');
            $table->string('local_ip')->nullable();
            $table->string('ip_mode')->default('固定');
            $table->string('name');
            $table->string('family');
            $table->text('description')->nullable();
            $table->integer('group_id');
            $table->string('style')->default('一般');
            $table->string('type')->default('一般');
            $table->integer('status')->default(1);
            $table->string('r1')->nullable();
            $table->string('r2')->nullable();
            $table->string('r3')->nullable();
            $table->string('r4')->nullable();
            $table->string('s1')->nullable();
            $table->string('s2')->nullable();
            $table->string('s3')->nullable();
            $table->string('s4')->nullable();
            $table->string('s5')->nullable();
            $table->string('s6')->nullable();
            $table->dateTime('synced_at')->nullable();
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
        Schema::dropIfExists('devices');
    }
}
