<?php

namespace App\Notifications;

use App\Channels\WhatsAppChannel;
use App\Models\Registration;
use Illuminate\Notifications\Notification;

class NewRegistrationNotification extends Notification
{
    public function __construct(private Registration $registration) {}

    public function via(object $notifiable): array
    {
        return ['database', WhatsAppChannel::class];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'registration_id'   => $this->registration->registration_id,
            'nama_siswa'        => $this->registration->nama_siswa,
            'jenis_pendaftaran' => $this->registration->jenis_pendaftaran,
            'message'           => "Pendaftaran baru dari {$this->registration->nama_siswa} ({$this->registration->jenis_pendaftaran})",
            'url'               => route('admin.pendaftaran.show', $this->registration->registration_id),
        ];
    }

    public function toWhatsApp(object $notifiable): string
    {
        return "[ICH Admin]\n\n"
             . "Pendaftaran baru dari *{$this->registration->nama_siswa}* "
             . "({$this->registration->jenis_pendaftaran}).\n\n"
             . "Silakan cek di panel admin.";
    }
}
