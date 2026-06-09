<?php

namespace App\Notifications;

use App\Channels\WhatsAppChannel;
use App\Models\FeeInstallment;
use Illuminate\Notifications\Notification;

class RegistrationFeeOverdueNotification extends Notification
{
    public function __construct(private FeeInstallment $installment) {}

    public function via(object $notifiable): array
    {
        return ['database', WhatsAppChannel::class];
    }

    public function toArray(object $notifiable): array
    {
        $namaSiswa    = $this->installment->registrationFee->student->nama_siswa;
        $jatuhTempo   = $this->installment->tanggal_jatuh_tempo->translatedFormat('d F Y');
        $jumlah       = number_format($this->installment->jumlah, 0, ',', '.');

        return [
            'installment_id'    => $this->installment->installment_id,
            'nama_siswa'        => $namaSiswa,
            'tanggal_jatuh_tempo' => $this->installment->tanggal_jatuh_tempo,
            'jumlah'            => $this->installment->jumlah,
            'message'           => "Cicilan biaya pendaftaran {$namaSiswa} sebesar Rp {$jumlah} jatuh tempo pada {$jatuhTempo}.",
            'url'               => route('pembayaran.pendaftaran.index'),
        ];
    }

    public function toWhatsApp(object $notifiable): string
    {
        $namaSiswa  = $this->installment->registrationFee->student->nama_siswa;
        $jatuhTempo = $this->installment->tanggal_jatuh_tempo->translatedFormat('d F Y');
        $jumlah     = number_format($this->installment->jumlah, 0, ',', '.');

        return "Assalamu'alaikum Bunda/Ayah,\n\n"
             . "Cicilan biaya pendaftaran ananda *{$namaSiswa}* sebesar *Rp {$jumlah}* "
             . "telah jatuh tempo pada {$jatuhTempo}.\n\n"
             . "Mohon segera melakukan pembayaran.\n\n"
             . "Terima kasih,\n"
             . "IQRA' Creative House";
    }
}
