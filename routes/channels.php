<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Authorisation for the private channels the trading dashboard subscribes to.
|
| Every channel is guarded on the authenticated user id. A signal feed is
| user-scoped because "Auto-Follow" and position state are per user, and the
| order channel carries fill details that must not leak between accounts.
|
*/

Broadcast::channel('App.Models.User.{id}', function (User $user, int $id) {
    return (int) $user->id === $id;
});

/**
 * The live signal feed. Any authenticated user may listen; the payload carries
 * no account data.
 */
Broadcast::channel('trading.signals', function (User $user) {
    return $user->status === 'active';
});

/**
 * Signals narrowed to one market type, e.g. trading.signals.futures.
 */
Broadcast::channel('trading.signals.{marketType}', function (User $user, string $marketType) {
    return $user->status === 'active' && in_array($marketType, ['spot', 'futures'], true);
});

/**
 * The signed-in user's own order and balance stream.
 */
Broadcast::channel('trading.orders.{userId}', function (User $user, int $userId) {
    return (int) $user->id === $userId;
});

/**
 * A single order's private lifecycle channel, used by the manual order ticket
 * to follow its own fill.
 */
Broadcast::channel('trading.order.{orderId}', function (User $user, int $orderId) {
    return \App\Models\TradeOrder::where('id', $orderId)
        ->where('user_id', $user->id)
        ->exists();
});
