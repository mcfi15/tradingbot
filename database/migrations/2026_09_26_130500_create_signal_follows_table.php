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
        if (!Schema::hasTable('signal_follows')) {
            Schema::create('signal_follows', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('trading_signal_id');

                // "auto" hands the signal to the queue engine, "manual" only
                // pre-fills the ticket and records the user's intent.
                $table->enum('mode', ['auto', 'manual'])->default('auto');
                $table->enum('status', ['pending', 'queued', 'executed', 'failed', 'skipped'])
                    ->default('pending');

                $table->unsignedBigInteger('trade_order_id')->nullable();

                // The user's risk rules at follow time. Frozen so an audit can
                // explain why a given size was chosen even after the user edits
                // their preferences later.
                $table->json('risk_snapshot')->nullable();

                $table->text('error_message')->nullable();
                $table->timestamps();

                $table->unique(['user_id', 'trading_signal_id'], 'signal_follows_user_signal_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('signal_follows')) {
            Schema::dropIfExists('signal_follows');
        }
    }
};
