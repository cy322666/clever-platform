<?php

namespace App\Notifications\Api;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Orchid\Platform\Notifications\DashboardChannel;
use Orchid\Platform\Notifications\DashboardMessage;

class amoCRMAuthException extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via(mixed $notifiable): array
    {
        return [DashboardChannel::class];
    }

    public function toDashboard($notifiable): DashboardMessage
    {
        return (new DashboardMessage())
            ->title('Ошибка авторизации amoCRM')
            ->message('При запросе к API amoCRM возникла ошибка. Проверьте подключение')
            ->action(route('account'));
    }
}