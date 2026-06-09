<?php

namespace App\Notifications;

use App\Channels\WhatsAppChannel;
use App\Models\SppPayment;
use Illuminate\Notifications\Notification;

class SppPaymentUploadedNotification extends Notification
{
    public function __construct(private SppPayment $payment) {}

    public function via(object $notifiable): array
    {
        return ['database', WhatsAppChannel::class];
    }

    public function toArray(object $notifiable): array
    {
        $namaSiswa = $this->payment->student->nama_siswa;
        $jumlah    = number_format($this->payment->jumlah_bayar, 0, ',', '.');

        return [
            'payment_id' => $this->payment->payment_id,
            'invoice_id' => $this->payment->invoice_id,
            'nama_siswa' => $namaSiswa,
            'jumlah'     => $this->payment->jumlah_bayar,
            'message'    => "{$namaSiswa} mengirim bukti pembayaran SPP sebesar Rp {$jumlah}. Menunggu konfirmasi.",
            'url'        => route('admin.keuangan.bukti-pembayaran', ['status' => 'pending']),
        ];
    }

    public function toWhatsApp(object $notifiable): string
    {
        $namaSiswa = $this->payment->student->nama_siswa;
        $jumlah    = number_format($this->payment->jumlah_bayar, 0, ',', '.');

        return "[ICH Admin]\n\n"
             . "*{$namaSiswa}* mengirim bukti pembayaran SPP sebesar *Rp {$jumlah}*.\n\n"
             . "Menunggu konfirmasi Anda.";
    }
}
