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
        if (!Schema::hasTable('trading_signals')) {
            Schema::create('trading_signals', function (Blueprint $table) {
                $table->id();

                // Canonical exchange symbol, e.g. BTCUSDT. Kept denormalised into
                // base/quote so the feed can filter without string parsing.
                $table->string('pair', 50);
                $table->string('base_asset', 20);
                $table->string('quote_asset', 20);

                $table->enum('market_type', ['spot', 'futures'])->default('spot');
                $table->enum('direction', ['buy', 'sell', 'long', 'short'])->default('buy');

                $table->decimal('entry_price', 18, 8);
                $table->decimal('target_1', 18, 8)->nullable();
                $table->decimal('target_2', 18, 8)->nullable();
                $table->decimal('target_3', 18, 8)->nullable();
                $table->decimal('stop_loss', 18, 8)->nullable();

                $table->decimal('confidence', 5, 2)->default(0);
                $table->enum('status', ['active', 'closed', 'cancelled'])->default('active');
                $table->string('source', 191)->nullable();
                $table->text('notes')->nullable();

                $table->bigInteger('signal_time');
                $table->bigInteger('expires_at')->nullable();
                $table->timestamps();

                $table->index(['status', 'market_type'], 'trading_signals_status_market_index');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('trading_signals')) {
            Schema::dropIfExists('trading_signals');
        }
    }
};
