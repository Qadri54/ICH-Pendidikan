<?php

namespace App\Notifications;

use App\Models\SppInvoice;
use Illuminate\Notifications\Notification;

class SppOverdueNotification extends Notification
{
    public function __construct(private SppInvoice $invoice) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $namaSiswa = $this->invoice->student->nama_siswa;
        $bulan     = \Carbon\Carbon::parse($this->invoice->tanggal_tahun)->translatedFormat('F Y');
        $jumlah    = number_format($this->invoice->jumlah, 0, ',', '.');

        return [
            'invoice_id'  => $this->invoice->invoice_id,
            'nama_siswa'  => $namaSiswa,
            'bulan'       => $bulan,
            'jumlah'      => $this->invoice->jumlah,
            'jatuh_tempo' => $this->invoice->jatuh_tempo,
            'message'     => "Tagihan SPP {$namaSiswa} bulan {$bulan} sebesar Rp {$jumlah} telah jatuh tempo.",
            'url'         => route('pembayaran.spp.index'),
        ];
    }
}
