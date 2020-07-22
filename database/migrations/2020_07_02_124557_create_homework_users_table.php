<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHomeworkUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('homework_users', function (Blueprint $table) {
            $table->id();
            $table->integer('homework_id');
            $table->integer('user_id');
            $table->timestamp('start_date');
            $table->timestamp('deadline')->nullable();
            $table->timestamp('completed_date')->nullable();
            $table->integer('point')->default(0);
            $table->integer('total_time')->default(0);
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
        Schema::dropIfExists('homework_users');
    }
}
