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
        if (!Schema::hasTable('exchange_connections')) {
            Schema::create('exchange_connections', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->enum('exchange', ['binance', 'bybit', 'mexc'])->default('binance');
                $table->enum('market_type', ['spot', 'futures'])->default('spot');

                // Encrypted payload. api_key and api_secret are both sealed with
                // Crypt::encryptString() before they ever reach the database.
                $table->text('api_key')->nullable();
                $table->text('api_secret')->nullable();

                // Non-reversible display hint so the UI can show which key is
                // stored without ever decrypting it for rendering.
                $table->string('api_key_hint', 191)->nullable();

                $table->string('label', 191)->nullable();
                $table->boolean('is_active')->default(true);

                $table->decimal('last_total_balance', 24, 8)->default(0);
                $table->bigInteger('last_synced_at')->nullable();
                $table->text('last_error')->nullable();

                $table->timestamps();

                $table->unique(['user_id', 'exchange', 'market_type'], 'exchange_connections_user_exchange_market_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('exchange_connections')) {
            Schema::dropIfExists('exchange_connections');
        }
    }
};
