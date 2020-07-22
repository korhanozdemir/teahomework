<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->integer('homework_id');
            $table->integer('question_type');
            $table->integer('question_index');
            $table->integer('option_count');
            $table->integer('answer');
            $table->integer('correct_answer');
            $table->integer('rating');
            $table->integer('ratable');
            $table->integer('is_question_pool');
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
        Schema::dropIfExists('questions');
    }
}
