<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateV2BeeTypeAttrTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('v2_bee_type_attr', function (Blueprint $table){
            
          $table->increments('id');
          $table->tinyInteger('code')->comment('代号');
          $table->string('name', 20)->comment('名称');
          $table->tinyInteger('order_id')->default(0)->comment('排序');
          $table->tinyInteger('is_show')->default(1)->comment('显示');
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
        Schema::dropIfExists('v2_bee_type_attr');
    }
}
