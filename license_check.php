<?php

/**
 * License gate diagnostic. Read-only: it makes no changes and calls the same
 * VmRuntime methods the gate calls, then reports which branch of STAGE_BOOT
 * the server takes.
 *
 * Usage on the server:  php license_check.php
 * Delete it when finished.
 */

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\LicensingEngine\VmRuntime;
use Illuminate\Support\Facades\Cache;

// Optional: php license_check.php your-domain.com
// Pretends the request arrived on that host, so you can exercise the
// non-local / network branch even when running from a local shell.
if (! empty($argv[1])) {
    $_SERVER['HTTP_X_FORWARDED_HOST'] = $argv[1];
}

function line(string $k, $v): void
{
    echo '  ' . str_pad($k, 26) . ' : ' . var_export($v, true) . PHP_EOL;
}

echo str_repeat('=', 68), PHP_EOL;
echo ' STAGE_BOOT gate diagnostic', PHP_EOL;
echo str_repeat('=', 68), PHP_EOL, PHP_EOL;

/* ---------------------------------------------------------------- inputs -- */
$domain   = VmRuntime::resolveDomain();
$isLocal  = VmRuntime::isLocalEnvironment($domain);
$key      = VmRuntime::resolveLicenseKey();
$server   = (string) config('site.setup_server_url');

echo '--- inputs the bytecode reads ---', PHP_EOL;
line('resolveDomain()', $domain);
line('isLocalEnvironment()', $isLocal);
line('PRODUCT_KEY', $key === null ? 'NULL  <-- branch 1 fails' : substr($key, 0, 8) . '... (len ' . strlen($key) . ')');
line('SETUP_SERVER_URL', $server === '' ? 'EMPTY  <-- branch 2 fails' : $server);
line('APP_ENV', config('app.env'));
line('APP_URL', config('app.url'));

$cached = Cache::get('sys_platform_integrity');
line('cached sys_platform_integrity', $cached === null ? 'none' : 'present');

/* ------------------------------------------------------------- branches -- */
echo PHP_EOL, '--- STAGE_BOOT decision tree ---', PHP_EOL;

if ($isLocal) {
    echo "  BRANCH 1  local host            => return TRUE  (gate open)", PHP_EOL;
    $verdict = 'PASS (local)';
} elseif ($key === null || $key === '') {
    echo "  BRANCH 2  no product key        => return FALSE (gate SHUT)", PHP_EOL;
    echo "            bytecode offset 21: PUSH_BOOL 0 / RETURN", PHP_EOL;
    $verdict = 'FAIL (no product key)';
} else {
    $token = $cached;
    $tokenOk = is_array($token)
        && ! empty($token['valid'])
        && ! empty($token['expires_at'])
        && $token['expires_at'] > time();

    if ($tokenOk) {
        echo "  BRANCH 3  valid cached token   => return TRUE  (gate open)", PHP_EOL;
        $verdict = 'PASS (cached token)';
    } elseif ($server === '') {
        echo "  BRANCH 4  no server url         => return FALSE (gate SHUT)", PHP_EOL;
        echo "            bytecode offset 76: PUSH_BOOL 0 / RETURN", PHP_EOL;
        $verdict = 'FAIL (no server url)';
    } else {
        echo "  reaching network call ...", PHP_EOL;
        $t0   = microtime(true);
        $resp = VmRuntime::executeCurl($server, 'api/v1/license/license-key/', $key, $domain);
        $ms   = round((microtime(true) - $t0) * 1000);
        $st   = (int) $resp['status'];

        echo PHP_EOL, '--- live server response (' . $ms . 'ms) ---', PHP_EOL;
        line('http status', $st);
        line('curl error', $resp['error'] ?: '(none)');
        line('body', substr((string) $resp['body'], 0, 400));

        echo PHP_EOL, '--- outcome ---', PHP_EOL;
        if ($st === 200 || $st === 201) {
            echo "  BRANCH 5  server 200/201       => return TRUE  (gate open)", PHP_EOL;
            $verdict = 'PASS (server 200)';
        } elseif ($st > 499 || $st < 400) {
            echo "  BRANCH 6  unreachable / 5xx    => grace path, needs token < 48h old", PHP_EOL;
            $sync = is_array($token) ? (int) ($token['last_sync'] ?? 0) : 0;
            $age  = $sync ? (time() - $sync) : null;
            line('cached token last_sync age', $age === null ? 'no token' : $age . 's');
            if ($age !== null && $age < 172800) {
                echo "              within 48h          => return TRUE  (gate open)", PHP_EOL;
                $verdict = 'PASS (48h grace)';
            } else {
                echo "              missing or stale     => return FALSE (gate SHUT)", PHP_EOL;
                $verdict = 'FAIL (server unreachable + no fresh token)';
            }
        } else {
            echo "  BRANCH 7  server 4xx            => cache purged, return FALSE (gate SHUT)", PHP_EOL;
            $verdict = 'FAIL (server rejected, HTTP ' . $st . ')';
        }
    }
}

echo PHP_EOL, str_repeat('=', 68), PHP_EOL;
echo ' VERDICT: ' . $verdict, PHP_EOL;
echo str_repeat('=', 68), PHP_EOL;

echo PHP_EOL, '--- what the middleware will do now ---', PHP_EOL;
$boot = VmRuntime::run(VmRuntime::STAGE_BOOT);
line('VmRuntime::run(STAGE_BOOT)', $boot);
if ($boot) {
    echo "  -> PlatformGuardMiddleware lets the request through.", PHP_EOL;
} else {
    echo "  -> PlatformGuardMiddleware returns the 503 'Platform License Required'", PHP_EOL;
    echo "     screen for every route not on the admin whitelist.", PHP_EOL;
}
