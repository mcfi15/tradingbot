<?php

namespace App\Models;

use App\Services\Exchange\ExchangeManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class ExchangeConnection extends Model
{
    protected $fillable = [
        'user_id',
        'exchange',
        'market_type',
        'label',
        'is_active',
    ];

    /**
     * Credential columns are never mass-assignable. They must go through
     * ExchangeConnection::storeCredentials() so they are always sealed with
     * Crypt::encryptString() before they touch the database.
     */
    protected $guarded = [
        'api_key',
        'api_secret',
        'api_key_hint',
        'last_total_balance',
        'last_synced_at',
        'last_error',
    ];

    /**
     * Defence in depth: even if a future refactor adds the credential columns
     * to $fillable, they can never be serialised into an array or JSON
     * response by accident.
     */
    protected $hidden = [
        'api_key',
        'api_secret',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_total_balance' => 'decimal:8',
        'last_synced_at' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orders()
    {
        return $this->hasMany(TradeOrder::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Credential handling
    |--------------------------------------------------------------------------
    */

    /**
     * Seal a key/secret pair and persist it.
     *
     * Both values are encrypted at rest with the application key. A mask is
     * stored separately so the UI can identify a connection without ever
     * decrypting anything for rendering.
     */
    public function storeCredentials(string $apiKey, string $apiSecret): self
    {
        $apiKey = trim($apiKey);
        $apiSecret = trim($apiSecret);

        $this->attributes['api_key'] = Crypt::encryptString($apiKey);
        $this->attributes['api_secret'] = Crypt::encryptString($apiSecret);
        $this->attributes['api_key_hint'] = static::maskKey($apiKey);

        return $this;
    }

    /**
     * Decrypt the API key. Only the exchange adapter layer may call this.
     */
    public function plainApiKey(): ?string
    {
        return $this->decryptOrNull('api_key');
    }

    /**
     * Decrypt the API secret. Only the exchange adapter layer may call this.
     */
    public function plainApiSecret(): ?string
    {
        return $this->decryptOrNull('api_secret');
    }

    /**
     * Build a configured adapter for this connection.
     *
     * Delegates to ExchangeManager::forConnection() rather than assembling the
     * adapter itself. The credentials are sealed with APP_KEY, so a key
     * rotation leaves them undecryptable and plainApiKey() returns null. Doing
     * this inline passed that null into a string-typed constructor, which
     * surfaced as an unhandled TypeError and a blank 500 rather than a
     * "re-enter your API key" message.
     *
     * @throws \App\Services\Exchange\Exceptions\ExchangeException
     */
    public function adapter(): \App\Services\Exchange\ExchangeServiceInterface
    {
        return app(ExchangeManager::class)->forConnection($this);
    }

    /**
     * A safe, renderable summary. Contains no secret material.
     *
     * `last_error` is carried through because an exchange that refuses a
     * connection is nearly impossible to debug when the page only says
     * "Unavailable". Exchange messages are already scrubbed of keys and
     * signatures at the point they are built, and it is truncated again here.
     */
    public function toDisplayArray(): array
    {
        return [
            'id' => $this->id,
            'exchange' => $this->exchange,
            'market_type' => $this->market_type,
            'label' => $this->label,
            'api_key_hint' => $this->api_key_hint,
            'is_active' => (bool) $this->is_active,
            'last_total_balance' => (float) $this->last_total_balance,
            'last_synced_at' => $this->last_synced_at,
            'last_synced_at_human' => $this->last_synced_at
                ? \Carbon\Carbon::createFromTimestamp($this->last_synced_at)->diffForHumans()
                : null,
            'has_error' => !empty($this->last_error),
            'last_error' => $this->last_error
                ? \Illuminate\Support\Str::limit($this->last_error, 300)
                : null,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Internals
    |--------------------------------------------------------------------------
    */

    /**
     * Decrypt a credential, returning null and logging a warning when the
     * payload is unreadable (for example after an APP_KEY rotation). A broken
     * credential must degrade to "not configured", never to a fatal error.
     */
    protected function decryptOrNull(string $attribute): ?string
    {
        $cipher = $this->getAttribute($attribute);

        if (empty($cipher)) {
            return null;
        }

        try {
            return Crypt::decryptString($cipher);
        } catch (\Throwable $e) {
            Log::warning('exchange_connections.credential_undecryptable', [
                'connection_id' => $this->id,
                'attribute' => $attribute,
                'reason' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Build a non-reversible display mask, e.g. "AbC1•••••••••9xYz".
     */
    public static function maskKey(string $apiKey): string
    {
        $length = strlen($apiKey);

        if ($length === 0) {
            return '';
        }

        if ($length <= 8) {
            return str_repeat('•', $length);
        }

        return substr($apiKey, 0, 4)
            . str_repeat('•', 8)
            . substr($apiKey, -4);
    }
}
