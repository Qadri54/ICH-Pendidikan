<?php

namespace App\Channels;

use App\Services\WhatsApp\WhatsAppService;
use Illuminate\Notifications\Notification;

class WhatsAppChannel
{
    public function __construct(private WhatsAppService $whatsAppService) {}

    public function send(object $notifiable, Notification $notification): void
    {
        if (! $this->whatsAppService->isEnabled()) {
            return;
        }

        $phone = $notifiable->no_hp ?? null;
        if (! $phone) {
            return;
        }

        if (! method_exists($notification, 'toWhatsApp')) {
            return;
        }

        $message = $notification->toWhatsApp($notifiable);

        $this->whatsAppService->send($phone, $message);
    }
}
