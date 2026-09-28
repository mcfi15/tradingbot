<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TradingSignal extends Model
{
    protected $fillable = [
        'pair',
        'base_asset',
        'quote_asset',
        'market_type',
        'direction',
        'entry_price',
        'target_1',
        'target_2',
        'target_3',
        'stop_loss',
        'confidence',
        'status',
        'source',
        'notes',
        'signal_time',
        'expires_at',
    ];

    protected $casts = [
        'entry_price' => 'decimal:8',
        'target_1' => 'decimal:8',
        'target_2' => 'decimal:8',
        'target_3' => 'decimal:8',
        'stop_loss' => 'decimal:8',
        'confidence' => 'decimal:2',
        'signal_time' => 'integer',
        'expires_at' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function follows()
    {
        return $this->hasMany(SignalFollow::class);
    }

    public function orders()
    {
        return $this->hasMany(TradeOrder::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopeForMarket(Builder $query, ?string $marketType): Builder
    {
        if (in_array($marketType, ['spot', 'futures'], true)) {
            $query->where('market_type', $marketType);
        }

        return $query;
    }

    public function scopeLive(Builder $query): Builder
    {
        $now = time();

        return $query->where('status', 'active')
            ->where(function (Builder $q) use ($now) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', $now);
            });
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getIsLongAttribute(): bool
    {
        return in_array($this->direction, ['buy', 'long'], true);
    }

    public function getIsExpiredAttribute(): bool
    {
        return !empty($this->expires_at) && $this->expires_at <= time();
    }

    /**
     * Signed percentage distance from entry to each target. Negative on a
     * short, because price moving *down* is a gain.
     */
    public function getRewardMapAttribute(): array
    {
        $entry = (float) $this->entry_price;

        if ($entry <= 0) {
            return [];
        }

        $sign = $this->is_long ? 1 : -1;

        $map = [];

        foreach (['target_1', 'target_2', 'target_3'] as $key) {
            $value = $this->{$key};

            if ($value === null) {
                continue;
            }

            $map[$key] = round(((((float) $value) - $entry) / $entry) * 100 * $sign, 2);
        }

        if ($this->stop_loss !== null) {
            $map['stop_loss'] = round(((((float) $this->stop_loss) - $entry) / $entry) * 100 * $sign, 2);
        }

        return $map;
    }

    /**
     * Payload broadcast to the browser and consumed by the signal card.
     */
    public function toFeedArray(): array
    {
        $rewards = $this->reward_map;

        return [
            'id' => $this->id,
            'pair' => $this->pair,
            'base_asset' => $this->base_asset,
            'quote_asset' => $this->quote_asset,
            'display_pair' => $this->base_asset . '/' . $this->quote_asset,
            'market_type' => $this->market_type,
            'direction' => $this->direction,
            'side' => $this->is_long ? 'buy' : 'sell',
            'is_long' => $this->is_long,
            'entry_price' => (float) $this->entry_price,
            'target_1' => $this->target_1 !== null ? (float) $this->target_1 : null,
            'target_2' => $this->target_2 !== null ? (float) $this->target_2 : null,
            'target_3' => $this->target_3 !== null ? (float) $this->target_3 : null,
            'stop_loss' => $this->stop_loss !== null ? (float) $this->stop_loss : null,
            'reward_pct' => $rewards,
            'confidence' => (float) $this->confidence,
            'status' => $this->status,
            'source' => $this->source,
            'signal_time' => $this->signal_time,
            'expires_at' => $this->expires_at,
            'created_at' => optional($this->created_at)->toIso8601String(),
        ];
    }
}
