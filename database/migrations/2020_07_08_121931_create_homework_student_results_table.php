<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHomeworkStudentResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('homework_student_results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('homework_id');
            $table->unsignedBigInteger('question_id');
            $table->integer('question_index');
            $table->text('matching')->nullable(); //TODO:: Silinecek
            $table->text('current_matching')->nullable(); //TODO:: Silinecek
            $table->text('answer')->nullable();
            $table->text('correct_answer')->nullable();
            $table->integer('type')->nullable();
            $table->unsignedInteger('time')->nullable();
            $table->text('option')->nullable();
            $table->text('option_matched')->nullable();
            $table->integer('success_percent')->nullable();
            $table->text('description')->nullable();
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
        Schema::dropIfExists('homework_student_results');
    }
}
