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
        if (!Schema::hasTable('user_trading_preferences')) {
            Schema::create('user_trading_preferences', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');

                // Master switch for the automated execution engine.
                $table->boolean('auto_trading_mode')->default(false);

                $table->enum('default_exchange', ['binance', 'bybit', 'mexc'])->nullable();
                $table->enum('default_market_type', ['spot', 'futures'])->default('spot');

                // Risk rules. max_trade_size is an absolute notional cap in the
                // quote asset; risk_percentage is a share of the wallet used to
                // derive size when the user has not given an explicit quantity.
                $table->decimal('max_trade_size', 20, 8)->default(0);
                $table->decimal('risk_percentage', 5, 2)->default(1.00);

                $table->boolean('auto_stop_loss')->default(true);
                $table->decimal('stop_loss_percentage', 5, 2)->default(2.00);
                $table->decimal('take_profit_percentage', 5, 2)->default(4.00);

                $table->unsignedInteger('max_concurrent_trades')->default(3);
                $table->decimal('max_daily_loss_percentage', 5, 2)->default(10.00);
                $table->decimal('slippage_tolerance', 5, 2)->default(0.50);

                $table->unsignedBigInteger('daily_loss_reset_at')->nullable();
                $table->timestamps();

                $table->unique('user_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('user_trading_preferences')) {
            Schema::dropIfExists('user_trading_preferences');
        }
    }
};
