<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| LỊCH CHẠY TỰ ĐỘNG (Task Scheduling)
|--------------------------------------------------------------------------
| Từ Laravel 11 trở đi, lịch chạy được khai báo ngay trong file này thay vì
| trong app/Console/Kernel.php như các phiên bản cũ.
|
| Để các lệnh dưới đây thực sự chạy, cần MỘT tiến trình nền:
|   - Khi dev / demo (Windows):  php artisan schedule:work
|   - Khi deploy thật (Linux):   thêm 1 dòng cron gọi `schedule:run` mỗi phút
|
| withoutOverlapping(): nếu lần chạy trước chưa xong mà tới giờ chạy tiếp,
| Laravel bỏ qua lần sau — tránh việc một yêu cầu bị xử lý hai lần và khách
| nhận hai email trùng nhau.
*/

// Tự động từ chối yêu cầu hoàn tiền quá 3 ngày admin chưa xác nhận.
// Quét mỗi giờ là đủ: hạn tính bằng ngày nên sai số 1 giờ không đáng kể,
// mà lại nhẹ cho server hơn nhiều so với quét mỗi phút.
Schedule::command('refunds:auto-reject')
    ->hourly()
    ->withoutOverlapping();

// Tự động hủy đơn VNPay khách bỏ dở quá 15 phút.
// Quét dày hơn (15 phút/lần) vì hạn ở đây tính bằng phút.
Schedule::command('orders:cancel-unpaid')
    ->everyFifteenMinutes()
    ->withoutOverlapping();
