<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up(): void
{
    Schema::create('student_models', function (Blueprint $table) {
        $table->id()->unique();
        $table->string('firstname');
        $table->string('middlename')->nullable();
        $table->string('lastname');
        $table->string('course');
        $table->string('year');
        $table->string('section');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_models');
    }
};
