<?php

/**
 * Standalone Installer for Foyana
 * Operating independently of Laravel to ensure requirement checks run first.
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../../storage/logs/installer-error.log');

/**
 * Diagnostic handler: surface fatal errors instead of a blank browser page.
 */
register_shutdown_function(function () {
    $e = error_get_last();
    if ($e === null) {
        return;
    }
    if (!in_array($e['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_RECOVERABLE_ERROR], true)) {
        return;
    }
    $msg = sprintf(
        "[%s] FATAL [%d] %s in %s:%d%s",
        date('Y-m-d H:i:s'),
        $e['type'],
        $e['message'],
        $e['file'],
        $e['line'],
        PHP_EOL
    );
    @file_put_contents(__DIR__ . '/../../storage/logs/installer-error.log', $msg, FILE_APPEND);

    if (!headers_sent()) {
        http_response_code(500);
    }
    echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Installer Error</title></head>'
        . '<body style="background:#03050a;color:#e2e8f0;font-family:monospace;padding:40px">'
        . '<h2 style="color:#fb7185">Installer Error</h2>'
        . '<pre style="background:#090c14;padding:20px;border:1px solid #333;border-radius:12px;white-space:pre-wrap">'
        . htmlspecialchars($e['message']) . "\n\nat " . htmlspecialchars($e['file']) . ':' . $e['line']
        . '</pre></body></html>';
});

session_start();

$current_step = $_GET['step'] ?? 'intro';
$storagePath = __DIR__ . '/../../storage';
$basePath = __DIR__ . '/../../';

// Basic safety: if installed.json exists, block installer
if (file_exists($storagePath . '/installed.json')) {
    header('Location: /');
    exit;
}

/**
 * Step Configuration & Validation
 */
$stepOrder = [
    'intro',
    'requirements',
    'server_config',
    'permissions',
    'files_functions',
    'license',
    'database',
    'admin'
];

$allSteps = [
    'intro' => [
        'title' => 'Terms & License',
        'subtitle' => 'Agreement & Conditions'
    ],
    'requirements' => [
        'title' => 'PHP & Extensions',
        'subtitle' => 'System Requirements'
    ],
    'server_config' => [
        'title' => 'Server Settings',
        'subtitle' => 'PHP Limits & Uploads'
    ],
    'permissions' => [
        'title' => 'Folder Permissions',
        'subtitle' => 'Writable Directories'
    ],
    'files_functions' => [
        'title' => 'Files & Functions',
        'subtitle' => 'Required PHP Functions'
    ],
    'license' => [
        'title' => 'Product License',
        'subtitle' => 'License Key Activation'
    ],
    'database' => [
        'title' => 'Database Setup',
        'subtitle' => 'Connection & Tables'
    ],
    'admin' => [
        'title' => 'Admin Account',
        'subtitle' => 'Create Administrator'
    ]
];

// Initialize progress if not set
if (!isset($_SESSION['completed_steps'])) {
    $_SESSION['completed_steps'] = ['intro'];
}

// Function to mark a step as completed
function completeStep($stepName)
{
    if (!in_array($stepName, $_SESSION['completed_steps'])) {
        $_SESSION['completed_steps'][] = $stepName;
    }
}

// Validation: Prevent skipping steps via URL
$currentIdx = array_search($current_step, $stepOrder);
if ($currentIdx === false) {
    header('Location: ?step=intro');
    exit;
}

// Find the furthest step the user is allowed to reach
$maxStepIdx = 0;
foreach ($stepOrder as $idx => $s) {
    if (in_array($s, $_SESSION['completed_steps'])) {
        $maxStepIdx = $idx;
    } else {
        break;
    }
}

// If user tries to access a step beyond what's completed + 1
if ($currentIdx > $maxStepIdx + 1) {
    $nextAllowedStep = $stepOrder[$maxStepIdx + ($maxStepIdx < count($stepOrder) - 1 ? 1 : 0)];
    header("Location: ?step=$nextAllowedStep");
    exit;
}

$step = $current_step;

/**
 * Helper to update .env
 */
function updateEnvValues(array $data, $basePath)
{
    $envPath = rtrim($basePath, '/\\') . DIRECTORY_SEPARATOR . '.env';
    if (file_exists($envPath)) {
        $content = file_get_contents($envPath);
        foreach ($data as $key => $value) {
            $quotedValue = $value;
            if (!is_numeric($value) && (strpos($value, '"') !== 0 || strrpos($value, '"') !== strlen($value) - 1)) {
                $quotedValue = '"' . str_replace('"', '\\"', $value) . '"';
            }

            if (preg_match("/^{$key}=/m", $content)) {
                $content = preg_replace("/^{$key}=.*/m", "{$key}={$quotedValue}", $content);
            } else {
                $content .= "\n{$key}={$quotedValue}";
            }
        }
        file_put_contents($envPath, $content);
    }
}

/**
 * Helper to read a value from .env or environment
 */
function getEnvValue($key, $basePath, $default = '')
{
    $val = getenv($key);
    if ($val !== false && $val !== '') {
        return trim($val);
    }

    $envPath = rtrim($basePath, '/\\') . DIRECTORY_SEPARATOR . '.env';
    if (!file_exists($envPath)) {
        return $default;
    }

    $content = file_get_contents($envPath);
    if (preg_match('/^\s*' . preg_quote($key, '/') . '\s*=\s*(.*)$/m', $content, $matches)) {
        $value = trim($matches[1]);
        if (!empty($value)) {
            $firstChar = $value[0];
            $lastChar = substr($value, -1);
            if (($firstChar === '"' && $lastChar === '"') || ($firstChar === "'" && $lastChar === "'")) {
                $value = substr($value, 1, -1);
            } else {
                $parts = explode('#', $value, 2);
                $value = trim($parts[0]);
            }
        }
        return $value;
    }

    return $default;
}

/**
 * Normalise a host string, preserving a non-default port.
 *
 * parse_url(..., PHP_URL_HOST) discards the port, which sent post-install
 * redirects to port 80 when the app was served on e.g. localhost:8000.
 */
function normalizeInstallHost($host)
{
    $host = trim((string) $host);
    if ($host === '') {
        return null;
    }

    $parsed = parse_url('http://' . $host);
    $name = $parsed['host'] ?? null;
    if (empty($name)) {
        return null;
    }

    $out = strtolower($name);

    if (!empty($parsed['port'])) {
        $out .= ':' . $parsed['port'];
    }

    return $out;
}

function resolveInstallDomain($basePath = null)
{
    // 1. Proxy / CDN forwarded host
    foreach ([
        'HTTP_X_FORWARDED_HOST',
        'HTTP_X_ORIGINAL_HOST',
    ] as $header) {
        if (!empty($_SERVER[$header])) {
            $parts = explode(',', $_SERVER[$header]);
            $host = normalizeInstallHost(trim($parts[0]));

            if (!empty($host)) {
                return $host;
            }
        }
    }

    // 2. RFC 7239 Forwarded header
    if (!empty($_SERVER['HTTP_FORWARDED'])) {
        if (preg_match('/(?:^|[;,])\s*host="?([^";,\s]+)"?/i', $_SERVER['HTTP_FORWARDED'], $match)) {
            $host = normalizeInstallHost($match[1]);

            if (!empty($host)) {
                return $host;
            }
        }
    }

    // 3. Normal host domain
    if (!empty($_SERVER['HTTP_HOST'])) {
        $host = normalizeInstallHost($_SERVER['HTTP_HOST']);

        if (!empty($host)) {
            return $host;
        }
    }

    // 4. APP_URL fallback
    if ($basePath && function_exists('getEnvValue')) {
        $appUrl = getEnvValue('APP_URL', $basePath);

        if (!empty($appUrl)) {
            $host = normalizeInstallHost($appUrl);

            if (!empty($host)) {
                return $host;
            }
        }
    }

    return 'localhost';
}

$error = $_SESSION['error'] ?? null;
unset($_SESSION['error']);

// Handle Post Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'next_step') {
        $step_to_complete = $_POST['current_step'] ?? '';

        if ($step_to_complete === 'permissions') {
            $dirs = [
                $storagePath,
                $storagePath . '/framework',
                $storagePath . '/app',
                $storagePath . '/logs',
                $basePath . 'bootstrap/cache'
            ];
            foreach ($dirs as $p) {
                if (!file_exists($p)) {
                    $_SESSION['error'] = "Required directory does not exist: " . htmlspecialchars(basename($p));
                    header("Location: ?step=permissions");
                    exit;
                }
                $cur = (int) substr(sprintf('%o', fileperms($p)), -3);
                if ($cur < 775) {
                    @chmod($p, 0775);
                    clearstatcache(true, $p);
                    $cur = (int) substr(sprintf('%o', fileperms($p)), -3);
                }
                if (!is_writable($p) || $cur < 775) {
                    $_SESSION['error'] = "Please set permissions to 0775 for storage and bootstrap/cache (current: 0{$cur}).";
                    header("Location: ?step=permissions");
                    exit;
                }
            }
        }

        completeStep($step_to_complete);

        $currentIdx = array_search($step_to_complete, $stepOrder);
        $nextStep = $stepOrder[$currentIdx + 1] ?? $step_to_complete;

        header("Location: ?step=$nextStep");
        exit;
    }

    if ($action === 'activate_license') {
        $product_key = trim($_POST['purchase_code'] ?? '');
        $domain = resolveInstallDomain($basePath);

        if (empty($product_key)) {
            $_SESSION['error'] = 'Please enter a valid purchase code / license key.';
            header('Location: ?step=license');
            exit;
        }

        $setupServerUrl = rtrim(getEnvValue('SETUP_SERVER_URL', $basePath, 'https://foyana.com'), '/');
        if (empty($setupServerUrl)) {
            $_SESSION['error'] = 'Licensing setup server URL is not configured. Please ensure SETUP_SERVER_URL is set in your .env file.';
            header('Location: ?step=license');
            exit;
        }
        
        file_put_contents($storagePath . '/temp-key.txt', $product_key);
        completeStep('license');
        completeStep('database');
        $_SESSION['db_skipped'] = true;
        header('Location: ?step=admin');
        exit;

        // try {
        //     $url = $setupServerUrl . '/api/v1/license/activate';

        //     $ch = curl_init($url);
        //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //     curl_setopt($ch, CURLOPT_POST, true);
        //     curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        //         'license_key' => $product_key,
        //         'domain' => $domain,
        //     ]));
        //     curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        //     curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
        //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        //     curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        //     curl_setopt($ch, CURLOPT_USERAGENT, 'Foyana-Installer/1.0.0');

        //     $response = curl_exec($ch);
        //     $curlError = curl_error($ch);
        //     $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        //     curl_close($ch);

        //     if ($response === false) {
        //         $_SESSION['error'] = 'Could not reach licensing server: ' . ($curlError ?: 'Connection timeout.');
        //         header('Location: ?step=license');
        //         exit;
        //     }

        //     $response_json = json_decode($response, true);

        //     if ($httpCode == 200 || $httpCode == 201) {
        //         file_put_contents($storagePath . '/temp-key.txt', $product_key);
        //         completeStep('license');
        //         header('Location: ?step=database');
        //         exit;
        //     } else {
        //         $_SESSION['error'] = $response_json['message'] ?? 'Activation failed (HTTP ' . $httpCode . '). Please check your purchase code.';
        //         header('Location: ?step=license');
        //         exit;
        //     }
        // } catch (Exception $e) {
        //     $_SESSION['error'] = "Activation Error: " . $e->getMessage();
        //     header('Location: ?step=license');
        //     exit;
        // }
    }

    if ($action === 'database_setup') {
        $db_host = $_POST['db_host'];
        $db_port = $_POST['db_port'];
        $db_name = $_POST['db_name'];
        $db_user = $_POST['db_user'];
        $db_pass = $_POST['db_password'] ?? '';
        $force_reset = isset($_POST['force_reset']) && $_POST['force_reset'] == '1';

        try {
            updateEnvValues([
                'DB_HOST' => $db_host,
                'DB_PORT' => $db_port,
                'DB_DATABASE' => $db_name,
                'DB_USERNAME' => $db_user,
                'DB_PASSWORD' => $db_pass,
                'APP_ENV' => 'local',
                'APP_DEBUG' => 'true'
            ], $basePath);

            if (!file_exists($basePath . 'vendor/autoload.php')) {
                throw new Exception("Vendor folder not found. Please run 'composer install'.");
            }
            require $basePath . 'vendor/autoload.php';
            $app = require_once $basePath . 'bootstrap/app.php';
            $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

            config([
                'database.connections.mysql.host' => $db_host,
                'database.connections.mysql.port' => $db_port,
                'database.connections.mysql.database' => $db_name,
                'database.connections.mysql.username' => $db_user,
                'database.connections.mysql.password' => $db_pass,
            ]);
            \Illuminate\Support\Facades\DB::purge('mysql');

            $tables = \Illuminate\Support\Facades\DB::select('SHOW TABLES');
            if (count($tables) > 0 && !$force_reset) {
                $_SESSION['db_collision'] = true;
                $_SESSION['db_params'] = $_POST;
                $_SESSION['error'] = "The database already contains existing tables.";
                header('Location: ?step=database');
                exit;
            }

            if ($force_reset) {
                $tables = \Illuminate\Support\Facades\DB::select('SHOW TABLES');
                $dbNameKey = "Tables_in_" . $db_name;

                \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
                foreach ($tables as $table) {
                    $tableName = $table->$dbNameKey;
                    \Illuminate\Support\Facades\Schema::dropIfExists($tableName);
                }
                \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();
            }

            $sqlFile = __DIR__ . '/database.sql';
            if (file_exists($sqlFile)) {
                $sql = file_get_contents($sqlFile);
                \Illuminate\Support\Facades\DB::unprepared($sql);
            }

            completeStep('database');

            header('Location: ?step=admin');
            exit;
        } catch (Exception $e) {
            $_SESSION['error'] = "Database Setup Failed: " . $e->getMessage();
            header('Location: ?step=database');
            exit;
        }
    }

    if ($action === 'skip_database') {
        completeStep('database');
        $_SESSION['db_skipped'] = true;
        header('Location: ?step=admin');
        exit;
    }

    if ($action === 'admin_setup') {
        $name = $_POST['name'];
        $user = $_POST['username'];
        $email = $_POST['email'];
        $pass = $_POST['password'];

        try {
            if (!file_exists($basePath . 'vendor/autoload.php')) {
                throw new Exception("Vendor folder not found. Please run 'composer install'.");
            }
            require $basePath . 'vendor/autoload.php';
            $app = require_once $basePath . 'bootstrap/app.php';
            $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

            \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);

            $publicStoragePath = $basePath . 'public/storage';
            if (file_exists($publicStoragePath)) {
                \Illuminate\Support\Facades\File::deleteDirectory($publicStoragePath);
            }

            \Illuminate\Support\Facades\Artisan::call('storage:link', ['--force' => true]);
            \Illuminate\Support\Facades\Artisan::call('optimize:clear');
            \Illuminate\Support\Facades\Artisan::call('key:generate', ['--force' => true]);
            app(\App\Services\Background\ResourceIndexWorker::class)->execute();

            $tempKeyFile = $storagePath . '/temp-key.txt';
            if (file_exists($tempKeyFile)) {
                $plainKey = file_get_contents($tempKeyFile);

                $envContent = file_get_contents($basePath . '.env');
                if (preg_match('/^APP_KEY=["\']?(.*?)["\']?$/m', $envContent, $matches)) {
                    $newAppKey = trim($matches[1]);

                    $encrypter = new \Illuminate\Encryption\Encrypter(
                        base64_decode(substr($newAppKey, 7)),
                        config('app.cipher') ?? 'AES-256-CBC'
                    );

                    $encrypted = $encrypter->encrypt($plainKey);
                    updateEnvValues(['PRODUCT_KEY' => $encrypted], $basePath);
                }
                @unlink($tempKeyFile);
            }

            \Illuminate\Support\Facades\DB::table('admins')->truncate();
            \Illuminate\Support\Facades\DB::table('admins')->insert([
                'name' => $name,
                'username' => $user,
                'email' => $email,
                'password' => \Illuminate\Support\Facades\Hash::make($pass),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $installDomain = resolveInstallDomain($basePath);
            $isHttps = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ||
                (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ||
                (isset($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on');
            $protocol = $isHttps ? "https" : "http";

            updateEnvValues([
                'APP_ENV' => 'production',
                'APP_DEBUG' => 'false',
                'APP_URL' => "$protocol://$installDomain",
            ], $basePath);

            file_put_contents($storagePath . '/installed.json', json_encode([
                'installation_date' => date('Y-m-d H:i:s'),
                'version' => '1.0.0'
            ]));

            $baseUrl = rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'])), '/\\');
            header("Location: $protocol://$installDomain$baseUrl/admin/login?installed=true");
            exit;
        } catch (Exception $e) {
            $_SESSION['error'] = "Final Setup Failed: " . $e->getMessage();
            header('Location: ?step=admin');
            exit;
        }
    }
}

// Compute progress percentage
$currentStepNumber = array_search($step, $stepOrder) + 1;
$totalSteps = count($stepOrder);
$progressPercentage = round(($currentStepNumber / $totalSteps) * 100);
?>
<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Foyana Platform Setup & Installation</title>
    <link rel="icon" type="image/png" href="/assets/images/favicon.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --cyan-glow: rgba(0, 245, 255, 0.15);
            --bg-void: #03050a;
            --bg-panel: #090c14;
            --bg-inner: #05070d;
        }

        body {
            font-family: 'JetBrains Mono', 'Plus Jakarta Sans', monospace;
            background-color: var(--bg-void);
            color: #e2e8f0;
            overflow-x: hidden;
        }

        .ambient-radial-1 {
            background: radial-gradient(circle at 10% 10%, rgba(0, 245, 255, 0.08) 0%, transparent 50%);
        }

        .ambient-radial-2 {
            background: radial-gradient(circle at 90% 90%, rgba(99, 102, 241, 0.08) 0%, transparent 50%);
        }

        .spinner {
            display: none;
            width: 18px;
            height: 18px;
            border: 2px solid rgba(0, 0, 0, 0.2);
            border-radius: 50%;
            border-top-color: #000;
            animation: spin 0.6s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .loading .spinner {
            display: inline-block;
        }

        .loading .btn-text {
            display: none;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.2);
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.08);
            border-radius: 9999px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: rgba(0, 245, 255, 0.3);
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center p-4 sm:p-8 bg-[#03050a] relative antialiased selection:bg-cyan-500/30 selection:text-cyan-200">
    <!-- Ambient Cyber Glows -->
    <div class="fixed inset-0 pointer-events-none ambient-radial-1"></div>
    <div class="fixed inset-0 pointer-events-none ambient-radial-2"></div>
    <div class="fixed inset-0 pointer-events-none bg-[radial-gradient(#ffffff08_1px,transparent_1px)] [background-size:24px_24px] opacity-40"></div>

    <div class="w-full max-w-6xl relative z-10 space-y-6">

        <!-- TOP HEADER -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4 pb-4 border-b border-white/[0.06]">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 text-[10px] font-bold uppercase tracking-[0.25em] mb-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-pulse"></span>
                    INSTALLATION WIZARD
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-cyan-100 to-cyan-400/70 tracking-tight">
                    Foyana Platform Installation
                </h1>
                <p class="text-slate-400 text-[10px] sm:text-xs mt-0.5 tracking-widest uppercase">
                    Step-by-step setup guide for your automated trading system
                </p>
            </div>

            <div class="flex items-center gap-4 bg-[#090c14] border border-white/[0.08] px-4 py-2 rounded-2xl">
                <div class="flex flex-col text-right">
                    <span class="text-[9px] uppercase font-bold text-slate-500 tracking-wider">Progress</span>
                    <span class="text-xs font-black text-cyan-400"><?= $currentStepNumber ?> of <?= $totalSteps ?> (<?= $progressPercentage ?>%)</span>
                </div>
                <div class="w-12 h-2 bg-white/10 rounded-full overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-cyan-400 to-indigo-500 rounded-full" style="width: <?= $progressPercentage ?>%"></div>
                </div>
            </div>
        </div>

        <!-- MAIN CONTAINER -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            <!-- LEFT SIDEBAR: STEP PROGRESSION MATRIX -->
            <div class="lg:col-span-4 xl:col-span-3">
                <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.08] backdrop-blur-2xl">
                    <div class="rounded-[2rem] bg-[#090c14]/95 p-4 space-y-2">
                        <div class="px-3 py-2 border-b border-white/[0.06] flex items-center justify-between mb-2">
                            <span class="text-[10px] font-black uppercase text-slate-400 tracking-[0.2em]">Setup Steps</span>
                            <span class="w-2 h-2 rounded-full bg-cyan-400 animate-ping"></span>
                        </div>

                        <?php
                        $foundCurrent = false;
                        $idx = 1;
                        foreach ($allSteps as $key => $meta):
                            $isActive = ($step === $key);
                            if ($isActive) $foundCurrent = true;
                            $isCompleted = !$foundCurrent && !$isActive;
                        ?>
                            <div class="p-3 rounded-2xl border transition-all duration-300 flex items-center gap-3 <?= $isActive ? 'bg-cyan-500/10 border-cyan-500/30 text-white shadow-[0_0_15px_rgba(0,245,255,0.15)]' : ($isCompleted ? 'bg-white/[0.015] border-white/[0.04] text-slate-300' : 'border-transparent text-slate-600 opacity-50') ?>">
                                <div class="w-7 h-7 rounded-xl flex items-center justify-center text-[10px] font-black shrink-0 <?= $isActive ? 'bg-cyan-400 text-black' : ($isCompleted ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-white/[0.03] border border-white/[0.06]') ?>">
                                    <?= $isCompleted ? '✓' : str_pad($idx++, 2, '0', STR_PAD_LEFT) ?>
                                </div>
                                <div class="min-w-0">
                                    <span class="text-xs font-bold block truncate <?= $isActive ? 'text-cyan-300' : 'text-slate-200' ?>"><?= $meta['title'] ?></span>
                                    <span class="text-[9px] text-slate-500 block truncate"><?= $meta['subtitle'] ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- RIGHT CHASSIS: ACTIVE STEP INTERACTION SURFACE -->
            <div class="lg:col-span-8 xl:col-span-9">
                <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.08] backdrop-blur-2xl">
                    <div class="rounded-[2rem] bg-[#090c14]/95 p-6 sm:p-10 space-y-8 text-xs min-h-[580px] flex flex-col justify-between">

                        <div>
                            <!-- GLOBAL ERROR NOTIFICATION -->
                            <?php if ($error): ?>
                                <div class="mb-6 p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 flex items-center gap-3 text-rose-300">
                                    <div class="w-8 h-8 rounded-xl bg-rose-500/20 flex items-center justify-center text-rose-400 font-bold shrink-0">
                                        ✕
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <span class="font-bold block text-xs uppercase tracking-wider text-rose-200">Error Occurred</span>
                                        <p class="text-[11px] text-rose-300/90"><?= htmlspecialchars($error) ?></p>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- STEP 1: INTRO & TERMS -->
                            <?php if ($step === 'intro'): ?>
                                <div class="space-y-6">
                                    <div class="pb-3 border-b border-white/[0.06]">
                                        <h3 class="text-sm font-black text-white uppercase tracking-wider">01 // License Agreement & Terms</h3>
                                        <p class="text-[10px] text-slate-400">Please review the software licensing terms before starting the installation.</p>
                                    </div>

                                    <div class="p-6 rounded-2xl bg-[#05070d] border border-white/[0.06] max-h-[300px] overflow-y-auto space-y-5 text-slate-300 leading-relaxed text-[11px]">
                                        <div class="space-y-1.5">
                                            <h4 class="text-white font-bold uppercase text-xs flex items-center gap-2">
                                                <span class="w-1.5 h-1.5 rounded-full bg-cyan-400"></span>
                                                1. Single Domain License
                                            </h4>
                                            <p class="text-slate-400 pl-3.5 border-l border-white/[0.08]">
                                                This software is licensed on a per-domain basis. Each purchase code is valid for one (1) live production website installation.
                                            </p>
                                        </div>

                                        <div class="space-y-1.5">
                                            <h4 class="text-white font-bold uppercase text-xs flex items-center gap-2">
                                                <span class="w-1.5 h-1.5 rounded-full bg-cyan-400"></span>
                                                2. Blockchain & Master Wallet Security
                                            </h4>
                                            <p class="text-slate-400 pl-3.5 border-l border-white/[0.08]">
                                                Foyana connects directly with supported blockchain networks for deposits and payouts. Master Wallet private keys are encrypted in your database using AES-256 and PHP Sodium.
                                            </p>
                                        </div>

                                        <div class="space-y-1.5">
                                            <h4 class="text-white font-bold uppercase text-xs flex items-center gap-2">
                                                <span class="w-1.5 h-1.5 rounded-full bg-cyan-400"></span>
                                                3. Updates & License Verification
                                            </h4>
                                            <p class="text-slate-400 pl-3.5 border-l border-white/[0.08]">
                                                Your license key must be activated to receive automated system updates, token price feeds, and platform features.
                                            </p>
                                        </div>
                                    </div>

                                    <div class="p-4 rounded-2xl bg-cyan-500/[0.03] border border-cyan-500/20 flex items-center gap-3">
                                        <input type="checkbox" id="agree-check" class="w-4 h-4 rounded border-white/20 bg-[#05070d] text-cyan-400 focus:ring-0 accent-cyan-400 cursor-pointer">
                                        <label for="agree-check" class="text-slate-300 text-xs cursor-pointer select-none">
                                            I have read and agree to the <strong class="text-white">Software License Terms</strong>.
                                        </label>
                                    </div>
                                </div>

                                <div class="pt-6 border-t border-white/[0.06] flex items-center justify-end">
                                    <button onclick="proceedToIntro()" id="start-btn" disabled
                                        class="px-8 py-3.5 rounded-xl bg-cyan-400 hover:bg-cyan-300 text-black font-black uppercase text-xs tracking-wider transition-all shadow-[0_0_20px_rgba(0,245,255,0.3)] flex items-center gap-2 opacity-40 cursor-not-allowed">
                                        <span class="btn-text">Accept & Continue →</span>
                                        <div class="spinner"></div>
                                    </button>
                                </div>

                                <script>
                                    const agreeCheck = document.getElementById('agree-check');
                                    const startBtn = document.getElementById('start-btn');
                                    agreeCheck.addEventListener('change', () => {
                                        if (agreeCheck.checked) {
                                            startBtn.disabled = false;
                                            startBtn.classList.remove('opacity-40', 'cursor-not-allowed');
                                        } else {
                                            startBtn.disabled = true;
                                            startBtn.classList.add('opacity-40', 'cursor-not-allowed');
                                        }
                                    });

                                    function proceedToIntro() {
                                        if (!startBtn.disabled) {
                                            startBtn.disabled = true;
                                            startBtn.classList.add('loading');
                                            const form = document.createElement('form');
                                            form.method = 'POST';
                                            form.innerHTML = '<input type="hidden" name="action" value="next_step"><input type="hidden" name="current_step" value="intro">';
                                            document.body.appendChild(form);
                                            form.submit();
                                        }
                                    }
                                </script>

                                <!-- STEP 2: REQUIREMENTS (WITH SODIUM EXTENSION) -->
                            <?php elseif ($step === 'requirements'): ?>
                                <?php
                                $phpVer = phpversion();
                                // Required PHP extensions (including sodium)
                                $reqExts = [
                                    'bcmath'    => 'Arbitrary Precision Math',
                                    'ctype'     => 'Character Type Checking',
                                    'curl'      => 'HTTP Client & API Calls',
                                    'dom'       => 'DOM Document Parsing',
                                    'fileinfo'  => 'File Type Inspection',
                                    'gd'        => 'Image & QR Code Processing',
                                    'gmp'       => 'Big Number Math Library',
                                    'json'      => 'JSON Data Serialization',
                                    'mbstring'  => 'Multibyte String Support',
                                    'openssl'   => 'OpenSSL Encryption',
                                    'pcre'      => 'Regular Expressions',
                                    'pdo'       => 'PDO Database Connectivity',
                                    'sodium'    => 'Sodium Cryptography & Security',
                                    'tokenizer' => 'PHP Tokenizer',
                                    'xml'       => 'XML Document Processing',
                                    'zip'       => 'ZIP Archive Compression'
                                ];
                                $allMet = version_compare($phpVer, '8.3.0', '>=');
                                ?>
                                <div class="space-y-6">
                                    <div class="pb-3 border-b border-white/[0.06]">
                                        <h3 class="text-sm font-black text-white uppercase tracking-wider">02 // PHP Version & Extensions</h3>
                                        <p class="text-[10px] text-slate-400">Verifying required PHP version and extensions on your server.</p>
                                    </div>

                                    <!-- PHP Version Card -->
                                    <div class="p-4 rounded-2xl bg-[#05070d] border border-white/[0.06] flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-xl bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 flex items-center justify-center font-bold text-xs">
                                                PHP
                                            </div>
                                            <div>
                                                <span class="text-white font-bold block text-xs">PHP Version</span>
                                                <span class="text-[9px] text-slate-500">Required: >= 8.3.0 (Recommended 8.3+)</span>
                                            </div>
                                        </div>
                                        <span class="px-3 py-1.5 rounded-xl border text-xs font-mono font-bold <?= version_compare($phpVer, '8.3.0', '>=') ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30' : 'bg-rose-500/10 text-rose-400 border-rose-500/30' ?>">
                                            <?= $phpVer ?>
                                        </span>
                                    </div>

                                    <!-- Extension Grid with Sodium -->
                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                                        <?php foreach ($reqExts as $ext => $description):
                                            $status = extension_loaded($ext);
                                            if (!$status) $allMet = false;
                                        ?>
                                            <div class="p-3.5 rounded-xl bg-[#05070d] border border-white/[0.06] flex items-center justify-between gap-2 hover:border-white/[0.15] transition-all">
                                                <div class="min-w-0">
                                                    <span class="text-xs font-bold block uppercase <?= $ext === 'sodium' ? 'text-cyan-400' : 'text-white' ?>">
                                                        <?= strtoupper($ext) ?>
                                                        <?= $ext === 'sodium' ? '★' : '' ?>
                                                    </span>
                                                    <span class="text-[8px] text-slate-500 block truncate"><?= $description ?></span>
                                                </div>
                                                <div class="w-6 h-6 rounded-lg flex items-center justify-center shrink-0 <?= $status ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-rose-500/20 text-rose-400 border border-rose-500/30' ?>">
                                                    <span class="text-[10px] font-black"><?= $status ? '✓' : '✕' ?></span>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                                <div class="pt-6 border-t border-white/[0.06] flex items-center justify-between gap-4">
                                    <button onclick="this.disabled=true; this.classList.add('loading'); window.location.reload()"
                                        class="px-5 py-3 rounded-xl bg-white/[0.04] hover:bg-white/[0.08] text-slate-300 font-bold uppercase text-[10px] tracking-wider transition-all flex items-center gap-2">
                                        <span class="btn-text">↻ Check Again</span>
                                        <div class="spinner"></div>
                                    </button>

                                    <?php if ($allMet): ?>
                                        <form method="POST">
                                            <input type="hidden" name="action" value="next_step">
                                            <input type="hidden" name="current_step" value="requirements">
                                            <button type="submit"
                                                class="px-8 py-3.5 rounded-xl bg-cyan-400 hover:bg-cyan-300 text-black font-black uppercase text-xs tracking-wider transition-all shadow-[0_0_20px_rgba(0,245,255,0.3)] flex items-center gap-2 cursor-pointer">
                                                <span class="btn-text">Continue to Server Settings →</span>
                                                <div class="spinner"></div>
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <div class="px-5 py-3 rounded-xl bg-rose-500/10 text-rose-400 font-bold uppercase text-[10px] border border-rose-500/20">
                                            Missing PHP Extensions
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- STEP 3: SERVER CONFIG -->
                            <?php elseif ($step === 'server_config'): ?>
                                <?php
                                $parse_size = function ($size) {
                                    if (!$size || $size == -1) return PHP_INT_MAX;
                                    $unit = preg_replace('/[^bkmgtp]/i', '', $size);
                                    $size = preg_replace('/[^0-9\.]/', '', $size);
                                    if ($unit) return round($size * pow(1024, stripos('bkmgtp', $unit[0])));
                                    return round($size);
                                };

                                $server_config = [
                                    'memory_limit' => [
                                        'recommended' => '512M',
                                        'current' => ini_get('memory_limit'),
                                        'label' => 'Memory Limit'
                                    ],
                                    'max_execution_time' => [
                                        'recommended' => '300',
                                        'current' => ini_get('max_execution_time'),
                                        'label' => 'Max Execution Time (seconds)'
                                    ],
                                    'max_input_time' => [
                                        'recommended' => '300',
                                        'current' => ini_get('max_input_time'),
                                        'label' => 'Max Input Time (seconds)'
                                    ],
                                    'post_max_size' => [
                                        'recommended' => '256M',
                                        'current' => ini_get('post_max_size'),
                                        'label' => 'POST Max Size'
                                    ],
                                    'upload_max_filesize' => [
                                        'recommended' => '256M',
                                        'current' => ini_get('upload_max_filesize'),
                                        'label' => 'Upload Max File Size'
                                    ],
                                ];

                                $all_config_met = true;
                                foreach ($server_config as $key => &$config) {
                                    if ($key == 'max_execution_time' || $key == 'max_input_time') {
                                        $config['status'] = (int) $config['current'] >= (int) $config['recommended'] || (int) $config['current'] == 0;
                                    } else {
                                        $config['status'] = $parse_size($config['current']) >= $parse_size($config['recommended']);
                                    }
                                    if (!$config['status']) $all_config_met = false;
                                }
                                ?>
                                <div class="space-y-6">
                                    <div class="pb-3 border-b border-white/[0.06]">
                                        <h3 class="text-sm font-black text-white uppercase tracking-wider">03 // Server Settings & Memory Limits</h3>
                                        <p class="text-[10px] text-slate-400">Ensures background workers, trading bots, and uploads run smoothly without timeouts.</p>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <?php foreach ($server_config as $key => $config): ?>
                                            <div class="p-4 rounded-2xl bg-[#05070d] border border-white/[0.06] flex items-center justify-between gap-4">
                                                <div class="min-w-0">
                                                    <span class="text-[9px] uppercase font-bold text-cyan-400 block tracking-wider"><?= $config['label'] ?></span>
                                                    <span class="text-xs font-bold text-white block mt-0.5"><?= $key ?></span>
                                                    <span class="text-[9px] text-slate-500 block">Recommended: <?= $config['recommended'] ?></span>
                                                </div>
                                                <div class="flex items-center gap-3">
                                                    <span class="px-3 py-1.5 rounded-xl border text-xs font-mono font-bold <?= $config['status'] ? 'bg-white/[0.02] text-white border-white/[0.08]' : 'bg-rose-500/10 text-rose-400 border-rose-500/30' ?>">
                                                        <?= $config['current'] ?: 'Unlimited' ?>
                                                    </span>
                                                    <div class="w-6 h-6 rounded-lg flex items-center justify-center <?= $config['status'] ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-rose-500/20 text-rose-400 border border-rose-500/30' ?>">
                                                        <span class="text-[10px] font-black"><?= $config['status'] ? '✓' : '✕' ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                                <div class="pt-6 border-t border-white/[0.06] flex items-center justify-between gap-4">
                                    <button onclick="this.disabled=true; this.classList.add('loading'); window.location.reload()"
                                        class="px-5 py-3 rounded-xl bg-white/[0.04] hover:bg-white/[0.08] text-slate-300 font-bold uppercase text-[10px] tracking-wider transition-all flex items-center gap-2">
                                        <span class="btn-text">↻ Check Again</span>
                                        <div class="spinner"></div>
                                    </button>

                                    <?php if ($all_config_met): ?>
                                        <form method="POST">
                                            <input type="hidden" name="action" value="next_step">
                                            <input type="hidden" name="current_step" value="server_config">
                                            <button type="submit"
                                                class="px-8 py-3.5 rounded-xl bg-cyan-400 hover:bg-cyan-300 text-black font-black uppercase text-xs tracking-wider transition-all shadow-[0_0_20px_rgba(0,245,255,0.3)] flex items-center gap-2 cursor-pointer">
                                                <span class="btn-text">Continue to Folder Permissions →</span>
                                                <div class="spinner"></div>
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <form method="POST">
                                            <input type="hidden" name="action" value="next_step">
                                            <input type="hidden" name="current_step" value="server_config">
                                            <button type="submit"
                                                class="px-8 py-3.5 rounded-xl bg-amber-400 hover:bg-amber-300 text-black font-black uppercase text-xs tracking-wider transition-all shadow-[0_0_20px_rgba(245,158,11,0.3)] flex items-center gap-2 cursor-pointer">
                                                <span class="btn-text">Proceed Anyway →</span>
                                                <div class="spinner"></div>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>

                                <!-- STEP 4: PERMISSIONS -->
                            <?php elseif ($step === 'permissions'): ?>
                                <?php
                                $dirs = [
                                    'storage' => $storagePath,
                                    'storage/framework' => $storagePath . '/framework',
                                    'storage/app' => $storagePath . '/app',
                                    'storage/logs' => $storagePath . '/logs',
                                    'bootstrap/cache' => $basePath . 'bootstrap/cache'
                                ];
                                $permissions = [];
                                foreach ($dirs as $name => $path) {
                                    $exists = file_exists($path);
                                    if ($exists) {
                                        $currentPerm = (int) substr(sprintf('%o', fileperms($path)), -3);
                                        if ($currentPerm < 775) {
                                            @chmod($path, 0775);
                                            clearstatcache(true, $path);
                                        }
                                    }
                                    $perms = $exists ? substr(sprintf('%o', fileperms($path)), -3) : 'N/A';
                                    $status = $exists && is_writable($path) && ((int) $perms >= 775);
                                    $permissions[$name] = [
                                        'path' => $path,
                                        'status' => $status,
                                        'recommended' => '0775',
                                        'current' => $perms,
                                        'exists' => $exists,
                                    ];
                                }
                                $permissions_met = !in_array(false, array_column($permissions, 'status'));
                                ?>
                                <div class="space-y-6">
                                    <div class="pb-3 border-b border-white/[0.06]">
                                        <h3 class="text-sm font-black text-white uppercase tracking-wider">04 // Folder Permissions</h3>
                                        <p class="text-[10px] text-slate-400">Verifying that cache, log, and storage folders are writable by the web server.</p>
                                    </div>

                                    <div class="space-y-3">
                                        <?php foreach ($permissions as $name => $data): ?>
                                            <div class="p-4 rounded-2xl bg-[#05070d] border border-white/[0.06] flex items-center justify-between gap-4">
                                                <div class="flex items-center gap-3 min-w-0">
                                                    <div class="w-8 h-8 rounded-xl bg-white/[0.03] border border-white/[0.08] flex items-center justify-center font-mono text-[10px] text-slate-400">
                                                        📁
                                                    </div>
                                                    <div class="min-w-0">
                                                        <span class="text-xs font-bold text-white block truncate"><?= $name ?></span>
                                                        <span class="text-[9px] text-slate-500 block truncate">Target: <?= $data['recommended'] ?></span>
                                                    </div>
                                                </div>

                                                <div class="flex items-center gap-3">
                                                    <span class="px-3 py-1 rounded-lg border text-xs font-mono font-bold <?= $data['status'] ? 'bg-white/[0.02] text-slate-300 border-white/[0.08]' : 'bg-rose-500/10 text-rose-400 border-rose-500/20' ?>">
                                                        <?= $data['current'] ?>
                                                    </span>
                                                    <div class="w-6 h-6 rounded-lg flex items-center justify-center <?= $data['status'] ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-rose-500/20 text-rose-400 border border-rose-500/30' ?>">
                                                        <span class="text-[10px] font-black"><?= $data['status'] ? '✓' : '✕' ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                                <div class="pt-6 border-t border-white/[0.06] flex items-center justify-between gap-4">
                                    <button onclick="this.disabled=true; this.classList.add('loading'); window.location.reload()"
                                        class="px-5 py-3 rounded-xl bg-white/[0.04] hover:bg-white/[0.08] text-slate-300 font-bold uppercase text-[10px] tracking-wider transition-all flex items-center gap-2">
                                        <span class="btn-text">↻ Check Again</span>
                                        <div class="spinner"></div>
                                    </button>

                                    <?php if ($permissions_met): ?>
                                        <form method="POST">
                                            <input type="hidden" name="action" value="next_step">
                                            <input type="hidden" name="current_step" value="permissions">
                                            <button type="submit"
                                                class="px-8 py-3.5 rounded-xl bg-cyan-400 hover:bg-cyan-300 text-black font-black uppercase text-xs tracking-wider transition-all shadow-[0_0_20px_rgba(0,245,255,0.3)] flex items-center gap-2 cursor-pointer">
                                                <span class="btn-text">Continue to Files & Functions →</span>
                                                <div class="spinner"></div>
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <div class="px-5 py-3 rounded-xl bg-rose-500/10 text-rose-400 font-bold uppercase text-[10px] border border-rose-500/20">
                                            Please set permissions to 775 for storage and bootstrap/cache
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- STEP 5: FILES & FUNCTIONS -->
                            <?php elseif ($step === 'files_functions'): ?>
                                <?php
                                $files = [
                                    '.env' => $basePath . '.env',
                                    'public/.htaccess' => __DIR__ . '/../.htaccess',
                                    'public/install/database.sql' => __DIR__ . '/database.sql',
                                    'storage/app/public' => $storagePath . '/app/public'
                                ];
                                $funcs = ['symlink', 'proc_open', 'popen', 'putenv', 'chmod', 'fsockopen'];
                                $allMet = true;
                                ?>
                                <div class="space-y-6">
                                    <div class="pb-3 border-b border-white/[0.06]">
                                        <h3 class="text-sm font-black text-white uppercase tracking-wider">05 // System Files & Functions</h3>
                                        <p class="text-[10px] text-slate-400">Validating essential system files and PHP helper functions.</p>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div class="space-y-3">
                                            <span class="text-[10px] font-black uppercase text-slate-400 tracking-wider block">Required Files</span>
                                            <?php foreach ($files as $name => $path):
                                                $exists = file_exists($path);
                                                if (!$exists) $allMet = false;
                                            ?>
                                                <div class="p-3 rounded-xl bg-[#05070d] border border-white/[0.06] flex items-center justify-between">
                                                    <span class="text-xs font-mono text-slate-300"><?= $name ?></span>
                                                    <span class="w-2 h-2 rounded-full <?= $exists ? 'bg-emerald-400 shadow-[0_0_8px_rgba(16,185,129,0.5)]' : 'bg-rose-400 animate-pulse' ?>"></span>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>

                                        <div class="space-y-3">
                                            <span class="text-[10px] font-black uppercase text-slate-400 tracking-wider block">PHP Functions</span>
                                            <?php foreach ($funcs as $f):
                                                $exists = function_exists($f);
                                                if (!$exists) $allMet = false;
                                            ?>
                                                <div class="p-3 rounded-xl bg-[#05070d] border border-white/[0.06] flex items-center justify-between">
                                                    <span class="text-xs font-mono text-slate-300"><?= $f ?>()</span>
                                                    <span class="text-[9px] font-black uppercase <?= $exists ? 'text-emerald-400' : 'text-rose-400' ?>"><?= $exists ? 'Enabled' : 'Disabled' ?></span>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="pt-6 border-t border-white/[0.06] flex items-center justify-between gap-4">
                                    <button onclick="this.disabled=true; this.classList.add('loading'); window.location.reload()"
                                        class="px-5 py-3 rounded-xl bg-white/[0.04] hover:bg-white/[0.08] text-slate-300 font-bold uppercase text-[10px] tracking-wider transition-all flex items-center gap-2">
                                        <span class="btn-text">↻ Check Again</span>
                                        <div class="spinner"></div>
                                    </button>

                                    <?php if ($allMet): ?>
                                        <form method="POST">
                                            <input type="hidden" name="action" value="next_step">
                                            <input type="hidden" name="current_step" value="files_functions">
                                            <button type="submit"
                                                class="px-8 py-3.5 rounded-xl bg-cyan-400 hover:bg-cyan-300 text-black font-black uppercase text-xs tracking-wider transition-all shadow-[0_0_20px_rgba(0,245,255,0.3)] flex items-center gap-2 cursor-pointer">
                                                <span class="btn-text">Continue to License →</span>
                                                <div class="spinner"></div>
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <div class="px-5 py-3 rounded-xl bg-rose-500/10 text-rose-400 font-bold uppercase text-[10px] border border-rose-500/20">
                                            Please enable required PHP functions in php.ini
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- STEP 6: LICENSE ACTIVATION -->
                            <?php elseif ($step === 'license'): ?>
                                <div class="space-y-6">
                                    <div class="pb-3 border-b border-white/[0.06]">
                                        <h3 class="text-sm font-black text-white uppercase tracking-wider">06 // Product License Activation</h3>
                                        <p class="text-[10px] text-slate-400">Enter your purchase code or license key to activate your installation.</p>
                                    </div>

                                    <form method="POST" class="space-y-6">
                                        <input type="hidden" name="action" value="activate_license">

                                        <div class="space-y-2">
                                            <label class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Purchase Code / License Key</label>
                                            <input type="text" name="purchase_code" required placeholder="XXXX-XXXX-XXXX-XXXX" value="8c4e17b2-5f93-4ad6-87c1-e209b635fa74"
                                                class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-5 py-4 text-sm font-mono tracking-widest focus:outline-none">
                                        </div>

                                        <div class="p-4 rounded-2xl bg-[#05070d] border border-white/[0.06] flex items-center gap-3">
                                            <span class="text-cyan-400 text-base">ℹ</span>
                                            <span class="text-slate-400 text-[11px]">
                                                Website Domain: <strong class="text-white font-mono"><?= htmlspecialchars(resolveInstallDomain($basePath)) ?></strong>
                                            </span>
                                        </div>

                                        <div class="pt-6 border-t border-white/[0.06] flex items-center justify-end">
                                            <button type="submit"
                                                class="px-8 py-3.5 rounded-xl bg-cyan-400 hover:bg-cyan-300 text-black font-black uppercase text-xs tracking-wider  transition-all shadow-[0_0_20px_rgba(0,245,255,0.3)] flex items-center gap-2 cursor-pointer">
                                                <span class="btn-text">Activate License →</span>
                                                <div class="spinner"></div>
                                            </button>
                                        </div>
                                    </form>
                                </div>
 
                                <!-- STEP 7: DATABASE SETUP -->
                            <?php elseif ($step === 'database'): ?>
                                <?php
                                $collision = $_SESSION['db_collision'] ?? false;
                                $params = $_SESSION['db_params'] ?? [];
                                ?>
                                <div class="space-y-6">
                                    <div class="pb-3 border-b border-white/[0.06]">
                                        <h3 class="text-sm font-black text-white uppercase tracking-wider">07 // Database Setup</h3>
                                        <p class="text-[10px] text-slate-400">Enter your MySQL database credentials to install database tables.</p>
                                    </div>

                                    <?php if ($collision): ?>
                                        <div class="p-5 rounded-2xl bg-amber-500/10 border border-amber-500/30 space-y-3">
                                            <span class="font-bold text-amber-300 block text-xs uppercase">Existing Tables Detected</span>
                                            <p class="text-amber-200/80 text-[11px]">This database already contains tables. You can overwrite them or use a different database name.</p>
                                            <form method="POST" class="flex items-center gap-3">
                                                <input type="hidden" name="action" value="database_setup">
                                                <input type="hidden" name="force_reset" value="1">
                                                <?php foreach ($params as $k => $v): ?>
                                                    <input type="hidden" name="<?= $k ?>" value="<?= htmlspecialchars($v) ?>">
                                                <?php endforeach; ?>
                                                <button type="submit" class="px-5 py-2 rounded-xl bg-amber-400 hover:bg-amber-300 text-black font-black uppercase text-[10px]">
                                                    Overwrite & Install
                                                </button>
                                                <a href="?step=database" onclick="<?php unset($_SESSION['db_collision']);
                                                                                    unset($_SESSION['db_params']); ?>" class="text-slate-400 hover:text-white text-[10px] font-bold">Cancel</a>
                                            </form>
                                        </div>
                                    <?php endif; ?>

                                    <form method="POST" class="space-y-4 <?= $collision ? 'opacity-30 pointer-events-none' : '' ?>">
                                        <input type="hidden" name="action" value="database_setup">

                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                            <div class="space-y-2">
                                                <label class="text-[10px] uppercase font-bold text-slate-400">Database Host</label>
                                                <input type="text" name="db_host" value="<?= htmlspecialchars($params['db_host'] ?? '127.0.0.1') ?>" required
                                                    class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-3 text-xs font-mono focus:outline-none">
                                            </div>

                                            <div class="space-y-2">
                                                <label class="text-[10px] uppercase font-bold text-slate-400">Port</label>
                                                <input type="text" name="db_port" value="<?= htmlspecialchars($params['db_port'] ?? '3306') ?>" required
                                                    class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-3 text-xs font-mono focus:outline-none">
                                            </div>
                                        </div>

                                        <div class="space-y-2">
                                            <label class="text-[10px] uppercase font-bold text-slate-400">Database Name</label>
                                            <input type="text" name="db_name" value="<?= htmlspecialchars($params['db_name'] ?? '') ?>" required placeholder="foyana"
                                                class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-3 text-xs font-mono focus:outline-none">
                                        </div>

                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                            <div class="space-y-2">
                                                <label class="text-[10px] uppercase font-bold text-slate-400">Username</label>
                                                <input type="text" name="db_user" value="<?= htmlspecialchars($params['db_user'] ?? 'root') ?>" required
                                                    class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-3 text-xs font-mono focus:outline-none">
                                            </div>

                                            <div class="space-y-2">
                                                <label class="text-[10px] uppercase font-bold text-slate-400">Password</label>
                                                <input type="password" name="db_password" value="<?= htmlspecialchars($params['db_password'] ?? '') ?>" placeholder="••••••••"
                                                    class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-3 text-xs font-mono focus:outline-none">
                                            </div>
                                        </div>

                                        <div class="pt-6 border-t border-white/[0.06] flex items-center justify-end gap-3">
                                            <button type="submit" name="action" value="skip_database"
                                                class="px-8 py-3.5 rounded-xl bg-slate-600 hover:bg-slate-500 text-white font-black uppercase text-xs tracking-wider transition-all border border-slate-500/30 flex items-center gap-2 cursor-pointer">
                                                <span class="btn-text">Skip →</span>
                                            </button>
                                            <button type="submit"
                                                class="px-8 py-3.5 rounded-xl bg-cyan-400 hover:bg-cyan-300 text-black font-black uppercase text-xs tracking-wider transition-all shadow-[0_0_20px_rgba(0,245,255,0.3)] flex items-center gap-2 cursor-pointer">
                                                <span class="btn-text">Install Database & Continue →</span>
                                                <div class="spinner"></div>
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                <!-- STEP 8: ADMINISTRATOR SETUP -->
                            <?php elseif ($step === 'admin'): ?>
                                <div class="space-y-6">
                                    <div class="pb-3 border-b border-white/[0.06]">
                                        <h3 class="text-sm font-black text-white uppercase tracking-wider">08 // Create Administrator Account</h3>
                                        <p class="text-[10px] text-slate-400">Create the primary administrator login for managing your platform.</p>
                                    </div>

                                    <?php if (!empty($_SESSION['db_skipped'])): ?>
                                        <div class="p-5 rounded-2xl bg-amber-500/10 border border-amber-500/30 space-y-3">
                                            <span class="font-bold text-amber-300 block text-xs uppercase">Database Setup Done Manually</span>
                                            <p class="text-amber-200/80 text-[11px] leading-relaxed">
                                                The database step was skipped. Make sure your database is created and migrated
                                                before submitting this form, otherwise the admin account cannot be saved.
                                            </p>
                                            <div class="space-y-2 font-mono text-[10px]">
                                                <div class="p-3 rounded-lg bg-black/40 border border-amber-500/20 text-amber-100">
                                                    <span class="text-slate-500"># 1. Set credentials in .env (DB_DATABASE, DB_USERNAME, DB_PASSWORD)</span><br>
                                                    <span class="text-cyan-300">php artisan config:clear</span>
                                                </div>
                                                <div class="p-3 rounded-lg bg-black/40 border border-amber-500/20 text-amber-100">
                                                    <span class="text-slate-500"># 2. Create the database in MySQL, then run:</span><br>
                                                    <span class="text-cyan-300">php artisan migrate --force</span>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <form method="POST" class="space-y-4">
                                        <input type="hidden" name="action" value="admin_setup">

                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                            <div class="space-y-2">
                                                <label class="text-[10px] uppercase font-bold text-slate-400">Admin Full Name</label>
                                                <input type="text" name="name" required placeholder="Administrator"
                                                    class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-3 text-xs focus:outline-none">
                                            </div>

                                            <div class="space-y-2">
                                                <label class="text-[10px] uppercase font-bold text-slate-400">Username</label>
                                                <input type="text" name="username" required placeholder="admin"
                                                    class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-3 text-xs font-mono focus:outline-none">
                                            </div>
                                        </div>

                                        <div class="space-y-2">
                                            <label class="text-[10px] uppercase font-bold text-slate-400">Email Address</label>
                                            <input type="email" name="email" required placeholder="admin@foyana.com"
                                                class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-3 text-xs font-mono focus:outline-none">
                                        </div>

                                        <div class="space-y-2">
                                            <label class="text-[10px] uppercase font-bold text-slate-400">Password</label>
                                            <div class="relative">
                                                <input type="password" name="password" id="admin-password" required minlength="8" placeholder="Minimum 8 characters"
                                                    class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl pl-4 pr-12 py-3 text-xs font-mono focus:outline-none">
                                                <button type="button" onclick="togglePasswordVisibility('admin-password', this)"
                                                    class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-white text-xs cursor-pointer">
                                                    👁
                                                </button>
                                            </div>
                                        </div>

                                        <div class="pt-6 border-t border-white/[0.06] flex items-center justify-end">
                                            <button type="submit"
                                                class="px-8 py-3.5 rounded-xl bg-cyan-400 hover:bg-cyan-300 text-black font-black uppercase text-xs tracking-wider transition-all shadow-[0_0_20px_rgba(0,245,255,0.3)] flex items-center gap-2 cursor-pointer">
                                                <span class="btn-text">Complete Setup & Launch →</span>
                                                <div class="spinner"></div>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- FOOTER -->
                        <div class="pt-4 border-t border-white/[0.04] flex flex-col sm:flex-row items-center justify-between gap-3 text-[10px] text-slate-500">
                            <span class="font-mono">Foyana Platform Setup</span>
                            <span class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                Ready to install
                            </span>
                        </div>

                    </div>
                </div>
            </div>

        </div>

    </div>

    <script>
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                if (this.checkValidity()) {
                    const btn = this.querySelector('button[type="submit"]');
                    if (btn) {
                        btn.disabled = true;
                        btn.classList.add('loading');
                    }
                }
            });
        });

        function togglePasswordVisibility(inputId, btn) {
            const input = document.getElementById(inputId);
            if (input.type === 'password') {
                input.type = 'text';
                btn.textContent = '🔒';
            } else {
                input.type = 'password';
                btn.textContent = '👁';
            }
        }
    </script>
</body>

</html>