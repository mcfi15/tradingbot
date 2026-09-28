<?php

namespace App\Events;

use App\Models\TradingSignal;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * A new trading signal is available.
 *
 * Broadcast on both a shared public channel and a market-specific channel so a
 * client subscribed to "futures only" does not receive spot traffic.
 *
 * ShouldBroadcast (queued, not ShouldBroadcastNow) because the event is fired
 * from request handling; broadcasting inline would add a WebSocket round trip to
 * the latency of whatever created the signal. The event is queued onto the
 * "trades" queue so it shares workers with order execution, which keeps
 * ordering: a signal broadcast cannot be overtaken by the order it triggered.
 *
 * The payload is built by TradingSignal::toFeedArray() and deliberately contains
 * market data only — no user, no balance, no credential.
 */
class SignalCreated implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Do not retry a broadcast. A missed signal frame is recoverable by the
     * client's poll fallback; retrying would delay the orders queued behind it.
     */
    public int $tries = 1;

    public function __construct(
        public TradingSignal $signal,
        public array $payload = []
    ) {
        $this->payload = $payload ?: $signal->toFeedArray();
    }

    /**
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('trading.signals'),
            new Channel('trading.signals.' . $this->signal->market_type),
        ];
    }

    /**
     * The name the frontend listens for: .trading.signal.created
     */
    public function broadcastAs(): string
    {
        return 'signal.created';
    }

    public function broadcastWith(): array
    {
        return [
            'signal' => $this->payload,
        ];
    }

    /**
     * Keep the event on the trades queue so it is ordered against the
     * ExecuteAutoTradeJob jobs it fans out to.
     */
    public function broadcastQueue(): string
    {
        return config('exchange.queues.trades', 'trades');
    }
}
