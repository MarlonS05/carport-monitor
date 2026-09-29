<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table): void {
            $table->foreignUuid('mobile_id')
                ->nullable()
                ->after('id')
                ->constrained('mobiles')
                ->nullOnDelete();
        });

        Schema::table('service_items', function (Blueprint $table): void {
            $table->dropUnique(['vehicle_id', 'external_id']);

            $table->foreignUuid('mobile_id')
                ->nullable()
                ->after('id')
                ->constrained('mobiles')
                ->nullOnDelete();

            $table->unique('external_id');
        });
    }

    public function down(): void
    {
        Schema::table('service_items', function (Blueprint $table): void {
            $table->dropUnique(['external_id']);
            $table->dropConstrainedForeignId('mobile_id');
            $table->unique(['vehicle_id', 'external_id']);
        });

        Schema::table('vehicles', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('mobile_id');
        });
    }
};
