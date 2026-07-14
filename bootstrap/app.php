<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Bypass ngrok browser warning (chặn CSS/JS load)
        $middleware->append(\App\Http\Middleware\NgrokBypass::class);

        // FIX: chặn cache toàn site (không chỉ nhóm route 'auth'), vì navbar hiển thị
        // trạng thái đăng nhập ("Xin chào, tên bạn") ở MỌI trang, kể cả trang công khai
        // như trang chủ. Nếu chỉ áp cho nhóm 'auth', bấm Back về lại trang chủ (nằm
        // ngoài nhóm đó) sau khi đăng xuất vẫn thấy bản cache cũ lúc còn đăng nhập.
        $middleware->append(\App\Http\Middleware\PreventBackHistoryCache::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        // Khi chưa đăng nhập mà cố vào route auth:
        // - Nếu là AJAX/API request → trả JSON 401
        // - Nếu là request thường → redirect về trang đăng nhập
        $exceptions->render(function (AuthenticationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vui lòng đăng nhập để tiếp tục.',
                ], 401);
            }
            return redirect()->route('login')
                ->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        });

    })->create();