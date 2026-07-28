<?php

namespace App\Notifications;

use App\Channels\WhatsAppChannel;
use App\Models\RegistrationTransaction;
use Illuminate\Notifications\Notification;

class RegistrationPaymentRejectedNotification extends Notification
{
    public function __construct(private RegistrationTransaction $transaction) {}

    public function via(object $notifiable): array
    {
        return ['database', WhatsAppChannel::class];
    }

    public function toArray(object $notifiable): array
    {
        $namaSiswa = $this->transaction->registrationFee->student->nama_siswa;
        $jumlah    = number_format($this->transaction->jumlah_bayar, 0, ',', '.');
        $kategori  = $this->transaction->payment_category === 'full' ? 'pelunasan' : 'cicilan';
        $reason    = $this->transaction->rejection_reason;

        return [
            'transaction_id' => $this->transaction->registration_transaction_id,
            'nama_siswa'     => $namaSiswa,
            'jumlah'         => $this->transaction->jumlah_bayar,
            'kategori'       => $kategori,
            'alasan'         => $reason,
            'message'        => "Pembayaran {$kategori} pendaftaran {$namaSiswa} sebesar Rp {$jumlah} ditolak." . ($reason ? " Alasan: {$reason}" : ''),
            'url'            => route('pembayaran.pendaftaran.index'),
        ];
    }

    public function toWhatsApp(object $notifiable): string
    {
        $namaSiswa = $this->transaction->registrationFee->student->nama_siswa;
        $jumlah    = number_format($this->transaction->jumlah_bayar, 0, ',', '.');
        $kategori  = $this->transaction->payment_category === 'full' ? 'pelunasan' : 'cicilan';
        $reason    = $this->transaction->rejection_reason;

        $msg = "Assalamu'alaikum Bunda/Ayah,\n\n"
             . "Mohon maaf, bukti {$kategori} biaya pendaftaran ananda *{$namaSiswa}* sebesar *Rp {$jumlah}* *tidak dapat dikonfirmasi*.";

        if ($reason) {
            $msg .= "\n\nAlasan: {$reason}";
        }

        $msg .= "\n\nSilakan upload ulang bukti pembayaran yang valid.\n\n"
              . "Terima kasih,\n"
              . "IQRA' Creative House";

        return $msg;
    }
}
