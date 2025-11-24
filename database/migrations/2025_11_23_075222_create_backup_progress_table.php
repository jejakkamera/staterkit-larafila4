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
        Schema::create('backup_progress', function (Blueprint $table) {
            $table->id();
            $table->string('backup_id')->unique();
            $table->string('status')->default('pending'); // pending, in_progress, completed, failed
            $table->integer('progress')->default(0); // 0-100
            $table->string('message')->nullable();
            $table->string('filename')->nullable();
            $table->integer('file_size')->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('backup_progress');
    }
};
