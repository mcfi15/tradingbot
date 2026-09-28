<?php

use App\Models\NotificationMessage;
use App\Models\Setting;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

// clear cache
Route::get('/clear-cache', function () {
    Artisan::call('cache:clear');
    // avoid redirect loop
    $previous_url = url()->previous();
    if ($previous_url == url()->current()) {
        return redirect()->route('home')->with('success', __('Cache cleared successfully'));
    }
    return redirect()->back()->with('success', __('Cache cleared successfully'));
})->name('clear-cache');



// create storage link forcefully
Route::get('create-storage-link', function () {
    Artisan::call('storage:link', [
        '--force' => true,
    ]);
    return response()->json([
        'status' => 'success',
        'message' => 'Storage link created successfully',
    ]);
})->name('create-storage-link');



// start schedule
Route::get('cronjob', function () {
    // 1. Prevent execution abort and raise time limit to 5 minutes
    ignore_user_abort(true);
    set_time_limit(300);

    // 2. Early Response Mechanism (Closes client connection in ~50ms to prevent timeouts)
    if (function_exists('fastcgi_finish_request')) {
        // Mode A: PHP-FPM (Nginx, cPanel with PHP-FPM, Forge)
        response()->json([
            'status' => 'success',
            'message' => 'Cron job initiated successfully',
        ])->send();
        fastcgi_finish_request();
    } elseif (!headers_sent()) {
        // Mode B: Non-PHP-FPM (Apache mod_php, LiteSpeed, IIS)
        $payload = json_encode([
            'status' => 'success',
            'message' => 'Cron job initiated successfully',
        ]);
        if (ob_get_level() > 0) {
            ob_end_clean();
        }
        header('Connection: close');
        header('Content-Type: application/json');
        header('Content-Length: ' . strlen($payload));
        echo $payload;
        if (function_exists('ob_flush')) {
            @ob_flush();
        }
        flush();
    }

    // 3. Execute daemon safely in background
    Artisan::call('foyana:scheduler-daemon');

    return response()->json([
        'status' => 'success',
        'message' => 'Cron job started successfully',
    ]);
})->name('cronjob');


// run migration
Route::get('run-migration', function () {
    // change env to local
    updateEnv('APP_ENV', 'local');
    updateEnv('APP_DEBUG', 'true');
    Artisan::call('migrate');


    // change back env
    updateEnv('APP_ENV', 'production');
    updateEnv('APP_DEBUG', 'false');
    return response()->json([
        'status' => 'success',
        'message' => 'Migration completed successfully',
    ]);
})->name('run-migration');
