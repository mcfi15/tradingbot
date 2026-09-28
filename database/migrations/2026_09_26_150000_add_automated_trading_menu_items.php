<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Seeds the sidebar navigation for the exchange-connected trading engine.
 *
 * The sidebar is database driven (see AppServiceProvider's view composer and the
 * admin MenuController), so the new pages are registered as menu rows rather than
 * hardcoded into the layout. That keeps them reorderable, relabelable and
 * switchable from the admin menu screen like every other entry.
 *
 * The rows are added as a new top-level "Automated Trading" group instead of
 * being folded into the existing "Trading Bots" / "Copy Trading" parents, because
 * those two are separate pre-existing features with their own data models.
 */
return new class extends Migration
{
    /**
     * House style: 20x20, stroke-width 2, currentColor.
     */
    private const ICON = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="M7 15l4-5 3 3 5-7"/></svg>';

    public function up(): void
    {
        if (! Schema::hasTable('menu_items')) {
            return;
        }

        // Already seeded: another install, or a re-run.
        if (DB::table('menu_items')->where('type', 'user')->where('route_wildcard', 'user.trading.*')->exists()) {
            return;
        }

        DB::table('menu_items')->insert([
            'parent_id' => null,
            'label' => 'Automated Trading',
            'route_name' => null,
            'route_wildcard' => 'user.trading.*',
            'url' => '#',
            'icon' => self::ICON,
            'type' => 'user',
            'sort_order' => 7,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $parentId = (int) DB::table('menu_items')->max('id');

        /*
         * Wildcards are deliberately narrow on the two order pages: a blanket
         * user.trading.orders.* would also match the log route, lighting up two
         * sidebar entries at once.
         */
        $children = [
            ['label' => 'Signals', 'route_name' => 'user.trading.signals.index', 'route_wildcard' => 'user.trading.signals.*'],
            ['label' => 'Place Order', 'route_name' => 'user.trading.orders.index', 'route_wildcard' => 'user.trading.orders.index'],
            ['label' => 'My Exchanges', 'route_name' => 'user.trading.exchanges.index', 'route_wildcard' => 'user.trading.exchanges.*'],
            ['label' => 'Trade Logs', 'route_name' => 'user.trading.orders.logs', 'route_wildcard' => 'user.trading.orders.logs'],
            ['label' => 'Risk Settings', 'route_name' => 'user.trading.preferences.index', 'route_wildcard' => 'user.trading.preferences.*'],
        ];

        $rows = [];
        $stamp = now();

        foreach ($children as $i => $child) {
            $rows[] = [
                'parent_id' => $parentId,
                'label' => $child['label'],
                'route_name' => $child['route_name'],
                'route_wildcard' => $child['route_wildcard'],
                'url' => null,
                'icon' => null,
                'type' => 'user',
                'sort_order' => $i + 1,
                'is_active' => true,
                'created_at' => $stamp,
                'updated_at' => $stamp,
            ];
        }

        DB::table('menu_items')->insert($rows);

        // The layout caches the menu tree for an hour.
        cache()->forget('user_menu_items');
    }

    public function down(): void
    {
        if (! Schema::hasTable('menu_items')) {
            return;
        }

        $parent = DB::table('menu_items')
            ->where('type', 'user')
            ->where('route_wildcard', 'user.trading.*')
            ->first();

        if (! $parent) {
            return;
        }

        DB::table('menu_items')->where('parent_id', $parent->id)->delete();
        DB::table('menu_items')->where('id', $parent->id)->delete();

        cache()->forget('user_menu_items');
    }
};
