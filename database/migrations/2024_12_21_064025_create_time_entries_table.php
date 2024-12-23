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

        Schema::create('time_entries', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // User who logged the time
            $table->foreignId('project_id')->nullable()->constrained()->onDelete('set null'); // Project associated with the time
            //$table->foreignId('task_id')->constrained()->onDelete('cascade'); // Task associated with the time
            $table->timestamp('start_time')->nullable(); // When the timer started
            $table->timestamp('end_time')->nullable(); // When the timer stopped
            $table->integer('duration')->nullable(); // Duration in seconds
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_entries');
    }
};
