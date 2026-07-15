<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends Notification
{
    public function __construct(public string $token)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('Yêu cầu đặt lại mật khẩu — Nexus Store')
            ->greeting('Xin chào' . ($notifiable->name ? ', ' . $notifiable->name : '') . '!')
            ->line('Bạn (hoặc ai đó) vừa yêu cầu đặt lại mật khẩu cho tài khoản Nexus Store gắn với email này.')
            ->action('Đặt lại mật khẩu', $url)
            ->line('Link này sẽ hết hạn sau 60 phút vì lý do bảo mật.')
            ->line('Nếu bạn không yêu cầu đặt lại mật khẩu, bạn có thể bỏ qua email này — mật khẩu hiện tại của bạn vẫn an toàn, không có gì thay đổi.')
            ->salutation('Trân trọng, Nexus Store');
    }
}