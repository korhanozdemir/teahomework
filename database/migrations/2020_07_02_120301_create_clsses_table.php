<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClssesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clsses', function (Blueprint $table) {
            $table->id();
            $table->string('class_name');
            $table->integer('class_level');
            $table->unsignedBigInteger('class_campus_id');
            $table->integer('is_class_mixed');
            $table->softDeletes();
            $table->timestamps();


        });

        Schema::table('clsses', function(Blueprint $table) {
            $table->foreign('class_campus_id')->references('id')->on('campuses');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clsses');
    }
}
