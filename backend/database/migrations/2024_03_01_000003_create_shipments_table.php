<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('origin');
            $table->string('destination');
            $table->string('cargo_type')->default('General');
            $table->decimal('cargo_weight_kg', 10, 2)->nullable();
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            $table->enum('status', ['pending', 'in_transit', 'delivered', 'cancelled'])->default('pending');
            $table->string('driver_name')->nullable();
            $table->string('vehicle_id')->nullable();
            $table->date('estimated_arrival')->nullable();
            $table->date('actual_arrival')->nullable();
            $table->decimal('distance_km', 10, 2)->nullable();
            $table->decimal('cost_estimate', 10, 2)->nullable();
            $table->json('optimized_route')->nullable();
            $table->text('ai_notes')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
