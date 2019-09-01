<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateV2DeptTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('v2_dept', function (Blueprint $table) {
            $table->bigIncrements('dept_id');
            $table->string('dept_name', 150)->comment('部门名称');
            $table->integer('up_dept')->comment('上级部门');
            $table->string('dept_key', 50)->comment('关系标志');
            $table->integer('tenant_id')->comment('公司');
            $table->integer('assess_hook')->comment('管理者');
            $table->string('order_no', 6)->comment('排序');
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
        Schema::dropIfExists('v2_dept');
    }
}
