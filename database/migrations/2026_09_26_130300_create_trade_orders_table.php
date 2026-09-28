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
        if (!Schema::hasTable('trade_orders')) {
            Schema::create('trade_orders', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('exchange_connection_id');
                $table->unsignedBigInteger('trading_signal_id')->nullable();

                // Idempotency key. A retry of ExecuteAutoTradeJob reuses the same
                // client_order_id so the exchange rejects the duplicate instead of
                // us opening a second position.
                $table->string('client_order_id', 64)->unique();
                $table->string('exchange_order_id', 191)->nullable();

                $table->enum('exchange', ['binance', 'bybit', 'mexc'])->default('binance');
                $table->enum('market_type', ['spot', 'futures'])->default('spot');
                $table->string('pair', 50);

                $table->enum('side', ['buy', 'sell'])->default('buy');
                $table->enum('direction', ['buy', 'sell', 'long', 'short'])->nullable();
                $table->enum('order_type', ['market', 'limit'])->default('market');
                $table->unsignedInteger('leverage')->nullable();

                $table->decimal('quantity', 20, 8)->default(0);
                $table->decimal('limit_price', 18, 8)->nullable();
                $table->decimal('filled_price', 18, 8)->nullable();
                $table->decimal('take_profit_price', 18, 8)->nullable();
                $table->decimal('stop_loss_price', 18, 8)->nullable();

                $table->decimal('notional', 20, 8)->default(0);
                $table->decimal('realised_pnl', 20, 8)->default(0);
                $table->decimal('fee', 20, 8)->default(0);

                $table->enum('status', [
                    'pending',
                    'open',
                    'filled',
                    'closed',
                    'cancelled',
                    'rejected',
                    'expired',
                ])->default('pending');

                $table->enum('origin', ['manual', 'auto'])->default('manual');

                $table->boolean('is_tp_hit')->default(false);
                $table->boolean('is_sl_hit')->default(false);

                $table->text('error_message')->nullable();
                $table->text('close_reason')->nullable();

                $table->bigInteger('submitted_at')->nullable();
                $table->bigInteger('filled_at')->nullable();
                $table->bigInteger('closed_at')->nullable();
                $table->timestamps();

                $table->index(['user_id', 'status'], 'trade_orders_user_status_index');
                $table->index(['user_id', 'created_at'], 'trade_orders_user_created_index');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('trade_orders')) {
            Schema::dropIfExists('trade_orders');
        }
    }
};
