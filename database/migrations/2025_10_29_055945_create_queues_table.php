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
        Schema::create('queues', function (Blueprint $table) {
            $table->char('queue_id', 26)->primary();
            $table->char('counter_id', 26);
            $table->string('queue_number', 10);
            $table->enum('status', ['waiting', 'called', 'done']);
            $table->timestamp('called_at')->nullable();
            $table->char('called_by', 26)->nullable();
            $table->timestamps();

            $table->foreign('counter_id')->references('counter_id')->on('counters')->onDelete('cascade');
            $table->foreign('called_by')->references('user_id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('queues');
    }
};
