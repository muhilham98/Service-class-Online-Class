<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('category',['website', 'mobile', 'machine learning', 'internet of things', 'cyber security', 'design', 'soft skills']);
            $table->enum('type',['premium', 'free']);
            $table->enum('level',['basic','mid', 'advance']);
            $table->integer('price')->default(0)->nullable();
            $table->boolean('certificate');
            $table->longText('description')->nullable();
            $table->string('code');
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
        Schema::dropIfExists('courses');
    }
}
