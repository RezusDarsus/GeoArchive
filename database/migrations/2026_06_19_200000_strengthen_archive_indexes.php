<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('artifacts', function (Blueprint $table) {
            $table->unique('title');
            $table->index(['category_id', 'created_at']);
            $table->index('location');
        });

        Schema::table('historical_events', function (Blueprint $table) {
            $table->unique('title');
            $table->index('location');
        });
    }

    public function down(): void
    {
        Schema::table('historical_events', function (Blueprint $table) {
            $table->dropUnique(['title']);
            $table->dropIndex(['location']);
        });

        Schema::table('artifacts', function (Blueprint $table) {
            $table->dropUnique(['title']);
            $table->dropIndex(['category_id', 'created_at']);
            $table->dropIndex(['location']);
        });
    }
};
