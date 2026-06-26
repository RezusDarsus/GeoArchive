<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('artifact_historical_event', function (Blueprint $table) {
            $table->foreignId('artifact_id')->constrained()->cascadeOnDelete();
            $table->foreignId('historical_event_id')->constrained()->cascadeOnDelete();
            $table->primary(['artifact_id', 'historical_event_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('artifact_historical_event');
    }
};
