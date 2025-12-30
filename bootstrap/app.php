<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

return Application::configure(basePath: dirname(__DIR__))
  ->withRouting(
    web: __DIR__ . '/../routes/web.php',
    api: __DIR__ . '/../routes/api.php',
    commands: __DIR__ . '/../routes/console.php',
    health: '/up',
  )
  ->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias([
      'verified' => \App\Http\Middleware\EnsureUserIsVerified::class,
    ]);
  })
  ->withExceptions(function (Exceptions $exceptions): void {
    $exceptions->render(function (AccessDeniedHttpException $e, $request) {
      return response()->json([
        'success' => false,
        'message' => 'You can not do this action',
      ], 403);
    });
  })
  ->withSchedule(function (Schedule $schedule) {
    $schedule->command('backup:run')->dailyAt('02:00');
  })
  ->create();
