<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->name('user.')
                ->group(base_path('routes/user.php'));

            Route::middleware('web')
                ->prefix('utils')
                ->name('utils.')
                ->group(base_path('routes/utils.php'));

            Route::middleware('api')
                ->prefix('api/v1')
                ->name('api.v1.')
                ->group(base_path('routes/apis/v1.php'));

            Route::middleware('web')
                ->prefix('admin')
                ->name('admin.')
                ->group(base_path('routes/admin.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');

        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
            \App\Http\Middleware\PlatformGuardMiddleware::class,
        ]);

        $middleware->alias([
            'otp.verified' => \App\Http\Middleware\OtpVerified::class,
            'admin.otp.verified' => \App\Http\Middleware\AdminOtpVerified::class,
            'user.status' => \App\Http\Middleware\CheckUserStatus::class,
            'user.kyc' => \App\Http\Middleware\KycMiddleware::class,
            'sandbox' => \App\Http\Middleware\SandBoxModeMiddleware::class,
        ]);

        $middleware->redirectGuestsTo(function ($request) {
            // Admin routes → admin login; everything else → user login
            return $request->is('admin/*') || $request->is('admin')
                ? route('admin.login')
                : route('user.login');
        });

        $middleware->redirectUsersTo(function ($request) {
            $isAdminRoute = $request->is('admin/*') || $request->is('admin');

            // On admin routes, send authenticated admins to the admin dashboard
            if ($isAdminRoute && auth()->guard('admin')->check()) {
                return route('admin.dashboard');
            }

            // On user routes (or any other route), send authenticated users to user dashboard.
            // Admins visiting user routes are NOT redirected here — they aren't authenticated
            // via the web guard and the guest middleware won't be triggered for them.
            return route('user.dashboard');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Throwable $e, $request) {
            if (!$request->expectsJson() || $request->isMethod('get')) {

                if (str_contains($e->getMessage(), 'Call to undefined method') || str_contains($e->getMessage(), 'does not exist')) {
                    // Extract controller and method if possible
                    preg_match('/method\s+([^\s:]+)::([^\s(]+)/', $e->getMessage(), $matches) ||
                        preg_match('/Method\s+\[([^\s\]]+)\]\s+does not exist/', $e->getMessage(), $matches);

                    $controller_method = $matches[1] ?? null;
                    $method = $matches[2] ?? null;

                    if (!$method && $controller_method) {
                        $parts = explode('::', $controller_method);
                        $controller = $parts[0] ?? null;
                        $method = $parts[1] ?? null;
                    } else {
                        $controller = $controller_method;
                    }

                    $template = config('site.template', 'york');
                    $view = view()->exists("templates.{$template}.coming-soon")
                        ? "templates.{$template}.coming-soon"
                        : (view()->exists('templates.york.coming-soon') ? 'templates.york.coming-soon' : null);

                    if ($view) {
                        return response()->view($view, [
                            'message' => $e->getMessage(),
                            'controller' => $controller,
                            'method' => $method,
                            'is_local' => app()->environment('local', 'staging'),
                        ], 500);
                    }
                }
            }
        });
    })
    ->withBroadcasting(
        __DIR__ . '/../routes/channels.php',
        [
            'prefix' => 'api',
            'middleware' => ['api', 'auth'],
        ],
    )
    ->withSchedule(function (\Illuminate\Console\Scheduling\Schedule $schedule) {
        // Enforce take-profit / stop-loss and reconcile pending orders.
        // withoutOverlapping matters here: a slow pass that overlaps the next
        // one risks double-closing a position.
        $schedule->command('trading:monitor')
            ->everyMinute()
            ->withoutOverlapping()
            ->runInBackground();

        // Refresh exchange balances. The job is unique per connection, so this
        // can safely fire every minute.
        $schedule->command('trading:sync-balances')
            ->everyMinute()
            ->withoutOverlapping();

        // Drain the trade and balance queues. These are separate queues so a
        // burst of signals cannot starve balance refreshes.
        $schedule->command('queue:work', [
            '--queue' => 'trades,balances,default',
            '--stop-when-empty' => true,
            '--max-time' => 50,
            '--tries' => 4,
        ])
            ->everyMinute()
            ->withoutOverlapping();
    })
    ->create();
