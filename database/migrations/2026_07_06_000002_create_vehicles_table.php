<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table): void {
            $table->id();
            $table->string('external_id')->unique();
            $table->string('name');
            $table->unsignedInteger('mileage')->default(0);
            $table->string('mileage_unit', 8)->default('km');
            $table->text('description')->nullable();
            $table->text('maintenance_schedule_image')->nullable();
            $table->string('user_manual_url')->nullable();
            $table->string('service_manual_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
