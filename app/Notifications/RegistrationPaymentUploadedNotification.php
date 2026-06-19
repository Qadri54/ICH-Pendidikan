<?php

namespace App\Notifications;

use App\Channels\WhatsAppChannel;
use App\Models\RegistrationTransaction;
use Illuminate\Notifications\Notification;

class RegistrationPaymentUploadedNotification extends Notification
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

        return [
            'transaction_id' => $this->transaction->registration_transaction_id,
            'nama_siswa'     => $namaSiswa,
            'jumlah'         => $this->transaction->jumlah_bayar,
            'kategori'       => $kategori,
            'message'        => "{$namaSiswa} mengirim bukti {$kategori} biaya pendaftaran sebesar Rp {$jumlah}. Menunggu konfirmasi.",
            'url'            => route('admin.pembayaran-pendaftaran.index', ['status' => 'pending']),
        ];
    }

    public function toWhatsApp(object $notifiable): string
    {
        $namaSiswa = $this->transaction->registrationFee->student->nama_siswa;
        $jumlah    = number_format($this->transaction->jumlah_bayar, 0, ',', '.');
        $kategori  = $this->transaction->payment_category === 'full' ? 'pelunasan' : 'cicilan';

        return "[ICH Admin]\n\n"
             . "*{$namaSiswa}* mengirim bukti {$kategori} biaya pendaftaran sebesar *Rp {$jumlah}*.\n\n"
             . "Menunggu konfirmasi Anda.";
    }
}
