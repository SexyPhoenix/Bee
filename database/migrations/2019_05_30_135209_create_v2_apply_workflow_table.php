<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateV2ApplyWorkflowTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('v2_bee_apply_workflow', function (Blueprint $table){
            
          $table->increments('flow_id');
          $table->Integer('apply_id')->comment('申请ID');
          $table->Integer('action_user_id')->comment('步骤处理人');
          $table->string('action_name', 10)->comment('步骤名称');
          $table->tinyInteger('status_id')->default(0)->comment('步骤处理状态值');
          $table->text('action_data')->comment('序列化步骤数据');
          $table->Integer('based_flow_id')->default(0)->comment('基于修改的ID');
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
        Schema::dropIfExists('v2_bee_apply_workflow');
    }
}
