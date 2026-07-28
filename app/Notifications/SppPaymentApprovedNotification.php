<?php

namespace App\Notifications;

use App\Channels\WhatsAppChannel;
use App\Models\SppPayment;
use Illuminate\Notifications\Notification;

class SppPaymentApprovedNotification extends Notification
{
    public function __construct(private SppPayment $payment) {}

    public function via(object $notifiable): array
    {
        return ['database', WhatsAppChannel::class];
    }

    public function toArray(object $notifiable): array
    {
        $namaSiswa = $this->payment->student->nama_siswa;
        $bulan     = \Carbon\Carbon::parse($this->payment->invoice->tanggal_tahun)->translatedFormat('F Y');
        $jumlah    = number_format($this->payment->jumlah_bayar, 0, ',', '.');

        return [
            'payment_id' => $this->payment->payment_id,
            'nama_siswa' => $namaSiswa,
            'bulan'      => $bulan,
            'jumlah'     => $this->payment->jumlah_bayar,
            'message'    => "Pembayaran SPP {$namaSiswa} bulan {$bulan} sebesar Rp {$jumlah} telah dikonfirmasi.",
            'url'        => route('pembayaran.spp.index'),
        ];
    }

    public function toWhatsApp(object $notifiable): string
    {
        $namaSiswa = $this->payment->student->nama_siswa;
        $bulan     = \Carbon\Carbon::parse($this->payment->invoice->tanggal_tahun)->translatedFormat('F Y');
        $jumlah    = number_format($this->payment->jumlah_bayar, 0, ',', '.');

        return "Assalamu'alaikum Bunda/Ayah,\n\n"
             . "Pembayaran SPP ananda *{$namaSiswa}* bulan *{$bulan}* sebesar *Rp {$jumlah}* telah *dikonfirmasi*.\n\n"
             . "Terima kasih atas pembayarannya.\n\n"
             . "IQRA' Creative House";
    }
}
