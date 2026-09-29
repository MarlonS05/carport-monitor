<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->string('external_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedInteger('mileage');
            $table->string('mileage_unit', 8)->default('km');
            $table->date('occurred_at');
            $table->timestamps();

            $table->unique(['vehicle_id', 'external_id']);
            $table->index('occurred_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_items');
    }
};
