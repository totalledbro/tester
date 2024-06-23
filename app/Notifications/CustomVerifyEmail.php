<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class CustomVerifyEmail extends BaseVerifyEmail
{
    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);
        $lastName = ucwords($notifiable->last_name);

        return (new MailMessage)
            ->subject('Verifikasi Email')
            ->greeting('Halo, ' . $lastName .'.')
            ->line('Tekan tombol dibawah ini untuk memverifikasi email anda.')
            ->action('Verifikasi Email', $verificationUrl)
            ->line('Jika anda tidak mendaftar menjadi anggota '. env('APP_NAME') .', abaikan email ini.')
            ->line('  ')
            ->line('Terima kasih.')
            ->salutation('Salam hangat, ' . env('APP_NAME'));
    }
}
