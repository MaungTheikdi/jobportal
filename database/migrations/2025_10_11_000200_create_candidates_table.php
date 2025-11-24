<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('candidates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('date_of_birth')->nullable();
            $table->string('nationality', 100)->nullable();
            $table->enum('gender', ['male','female','other'])->nullable();
            $table->string('resume_path')->nullable();
            $table->string('visa_status', 100)->nullable();
            $table->enum('japanese_level', ['N1','N2','N3','N4','N5','None'])->default('None');
            $table->string('current_location')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidates');
    }
};
