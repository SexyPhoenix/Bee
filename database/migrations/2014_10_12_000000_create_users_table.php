<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->comment('姓名');
            $table->string('job_no', 6)->unique()->comment('工号');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->integer('manager')->default(1)->comment('主管');
            $table->rememberToken();
            $table->string('join_date', 8)->comment('加入日期');
            $table->string('positive_date', 8)->nullable()->comment('转正日期');
            $table->string('work_begin_date', 8)->comment('开始工作日期');
            $table->string('region', 50)->comment('工作区域');
            $table->tinyInteger('crop_id')->default(1)->comment('公司');
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
        Schema::dropIfExists('users');
    }
}
