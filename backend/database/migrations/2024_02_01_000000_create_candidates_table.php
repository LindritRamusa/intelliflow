<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('candidates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('applied_role');
            $table->text('cv_text')->nullable();
            $table->unsignedTinyInteger('ai_score')->nullable();
            $table->text('ai_analysis')->nullable();
            $table->json('ai_strengths')->nullable();
            $table->json('ai_weaknesses')->nullable();
            $table->json('skills')->nullable();
            $table->enum('status', ['pending', 'review', 'shortlisted', 'interview', 'rejected', 'hired'])->default('pending');
            $table->text('notes')->nullable();
            $table->string('source')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidates');
    }
};
