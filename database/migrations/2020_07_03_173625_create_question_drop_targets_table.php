<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionDropTargetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('question_drop_targets', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('question_id');
            $table->string('bounds');
            $table->string('anchor');
            $table->integer('checked');
            $table->integer('correct_option_id');
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
        Schema::dropIfExists('question_drop_targets');
    }
}
