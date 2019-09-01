<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateV2BeeApplyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('v2_bee_apply', function (Blueprint $table){
            
          $table->increments('id');
          //$table->string('name', 20)->comment('名称');
          $table->integer('user_id')->comment('申请人');
          $table->tinyInteger('type_id')->comment('假种');
          $table->date('apply_date')->comment('申请日期');
          $table->tinyInteger('time_section')->comment('时段');          
          $table->string('start_time', 5)->nullable()->comment('开始时间');
          $table->string('end_time', 5)->nullable()->comment('结束时间');
          $table->tinyInteger('duration')->comment('度量 时/次');
          $table->tinyInteger('stage_id')->default(0)->comment('阶段');
          $table->tinyInteger('status_id')->default(0)->comment('状态');
          $table->integer('flow_user_id')->default(0)->comment('当前操作人');
          $table->char('flow_action', 4)->nullable()->comment('当前操作'); 
          $table->integer('handover')->nullable()->comment('交接人');
          $table->string('attach', 20)->nullable()->comment('附件');
          $table->text('meta')->nullable()->comment('元数据');                    
          $table->text('desc')->nullable()->comment('描述');         
          $table->string('region', 30)->default('cn-320000-320100')->comment('地区');
          $table->tinyInteger('crop_id')->default(1)->comment('公司');
          $table->integer('parent_id')->default(0)->comment('父申请ID');          
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
        Schema::dropIfExists('v2_bee_apply');
    }
}
