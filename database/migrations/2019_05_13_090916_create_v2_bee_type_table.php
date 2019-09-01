<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateV2BeeTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('v2_bee_type', function (Blueprint $table){
            
          $table->increments('id');
          $table->string('name', 20)->comment('名称');
          $table->tinyInteger('order_id')->default(0)->comment('排序');
          $table->tinyInteger('classify')->default(1)->comment('分类');
          $table->tinyInteger('is_manage')->default(0)->comment('管理假');
          $table->text('desc')->nullable()->comment('描述');
          $table->softDeletes();
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
        Schema::dropIfExists('v2_bee_type');
    }
}
