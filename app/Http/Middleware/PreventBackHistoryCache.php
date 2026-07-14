<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * FIX: sau khi đăng xuất, bấm nút Back của trình duyệt vẫn thấy trang cũ hiện
 * thông tin tài khoản đã đăng xuất — do trình duyệt phục vụ trang từ bộ nhớ
 * đệm back-forward cache (bfcache)/cache thông thường thay vì tải lại từ
 * server. Đây chỉ là ảo giác hiển thị (session ở server đã bị huỷ thật sự,
 * tải lại trang là mất ngay), nhưng vẫn gây hiểu lầm về mặt bảo mật.
 *
 * Middleware này thêm header chặn cache cho mọi trang yêu cầu đăng nhập,
 * buộc trình duyệt luôn tải lại từ server thay vì lấy từ cache khi bấm Back.
 */
class PreventBackHistoryCache
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $response->headers->set('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        return $response;
    }
}