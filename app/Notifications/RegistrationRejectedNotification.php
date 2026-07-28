<?php

namespace App\Notifications;

use App\Channels\WhatsAppChannel;
use App\Models\Registration;
use Illuminate\Notifications\Notification;

class RegistrationRejectedNotification extends Notification
{
    public function __construct(private Registration $registration) {}

    public function via(object $notifiable): array
    {
        return ['database', WhatsAppChannel::class];
    }

    public function toArray(object $notifiable): array
    {
        $reason = $this->registration->rejection_reason;

        return [
            'registration_id' => $this->registration->registration_id,
            'nama_siswa'      => $this->registration->nama_siswa,
            'alasan'          => $reason,
            'message'         => "Pendaftaran {$this->registration->nama_siswa} tidak dapat diproses." . ($reason ? " Alasan: {$reason}" : ''),
            'url'             => route('pendaftaran'),
        ];
    }

    public function toWhatsApp(object $notifiable): string
    {
        $reason = $this->registration->rejection_reason;

        $msg = "Assalamu'alaikum Bunda/Ayah,\n\n"
             . "Mohon maaf, pendaftaran ananda *{$this->registration->nama_siswa}* belum dapat kami terima.";

        if ($reason) {
            $msg .= "\n\nAlasan: {$reason}";
        }

        $msg .= "\n\nSilakan hubungi pihak sekolah untuk informasi lebih lanjut.\n\n"
              . "Terima kasih,\n"
              . "IQRA' Creative House";

        return $msg;
    }
}
