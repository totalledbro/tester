<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class CustomResetPassword extends BaseResetPassword
{
    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $resetUrl = url(route('password.reset', ['token' => $this->token, 'email' => $notifiable->email], false));
        $lastName = ucwords($notifiable->last_name);

        return (new MailMessage)
            ->subject('Reset Password')
            ->greeting('Halo! ' . $lastName . '.')
            ->line('Anda menerima email ini karena kami menerima permintaan reset password anda. Silakan klik tombol di bawah untuk memulai proses reset password anda.')
            ->action('Reset Password', $resetUrl)
            ->line('Jika anda tidak melakukan reset password, abaikan email ini.')
            ->line(' ')
            ->line('Terima kasih.')
            ->salutation('Salam hangat, ' . env('APP_NAME'));
    }
}
