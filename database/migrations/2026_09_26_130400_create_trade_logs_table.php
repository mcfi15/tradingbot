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
        if (!Schema::hasTable('trade_logs')) {
            Schema::create('trade_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('trade_order_id')->nullable();
                $table->unsignedBigInteger('trading_signal_id')->nullable();

                $table->string('exchange', 50)->nullable();
                $table->string('pair', 50)->nullable();
                $table->enum('market_type', ['spot', 'futures'])->nullable();

                $table->enum('level', ['info', 'warning', 'error', 'critical'])->default('info');
                $table->string('action', 100)->nullable();
                $table->text('message');

                // Sanitised request/response metadata. Anything resembling a
                // secret is redacted by TradeLogger before it reaches here.
                $table->json('context')->nullable();

                $table->timestamps();

                $table->index(['user_id', 'created_at'], 'trade_logs_user_created_index');
                $table->index(['level', 'created_at'], 'trade_logs_level_created_index');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('trade_logs')) {
            Schema::dropIfExists('trade_logs');
        }
    }
};
