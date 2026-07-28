<?php

namespace App\Notifications;

use App\Channels\WhatsAppChannel;
use App\Models\Registration;
use Illuminate\Notifications\Notification;

class RegistrationApprovedNotification extends Notification
{
    public function __construct(private Registration $registration) {}

    public function via(object $notifiable): array
    {
        return ['database', WhatsAppChannel::class];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'registration_id' => $this->registration->registration_id,
            'nama_siswa'      => $this->registration->nama_siswa,
            'message'         => "Pendaftaran {$this->registration->nama_siswa} telah disetujui. Selamat bergabung di IQRA' Creative House!",
            'url'             => route('pendaftaran'),
        ];
    }

    public function toWhatsApp(object $notifiable): string
    {
        return "Assalamu'alaikum Bunda/Ayah,\n\n"
             . "Pendaftaran ananda *{$this->registration->nama_siswa}* telah *disetujui*.\n\n"
             . "Selamat bergabung di IQRA' Creative House!\n\n"
             . "Silakan login untuk melihat informasi selengkapnya.\n\n"
             . "Terima kasih,\n"
             . "IQRA' Creative House";
    }
}
