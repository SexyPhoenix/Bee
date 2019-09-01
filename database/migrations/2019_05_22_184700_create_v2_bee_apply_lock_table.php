<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateV2BeeApplyLockTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('v2_bee_apply_lock', function (Blueprint $table){
            
          $table->increments('id');
          $table->string('lock_date', 6)->comment('月份');
          $table->tinyInteger('status_id')->default(1)->comment('锁定');
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
        Schema::dropIfExists('v2_bee_apply_lock');
    }
}
