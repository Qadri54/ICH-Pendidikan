<?php

namespace App\Notifications;

use App\Channels\WhatsAppChannel;
use App\Models\StudentReportCard;
use Illuminate\Notifications\Notification;

class ReportCardApprovedNotification extends Notification
{
    public function __construct(private StudentReportCard $reportCard) {}

    public function via(object $notifiable): array
    {
        return ['database', WhatsAppChannel::class];
    }

    public function toArray(object $notifiable): array
    {
        $namaSiswa = $this->reportCard->student->nama_siswa;
        $period    = $this->reportCard->period;
        $semester  = "Semester {$period->semester} T.A {$period->tahun_ajaran}";

        return [
            'report_card_id' => $this->reportCard->report_card_id,
            'nama_siswa'     => $namaSiswa,
            'semester'       => $semester,
            'message'        => "Raport {$namaSiswa} {$semester} telah tersedia. Silakan login untuk melihat dan mengunduh.",
            'url'            => route('akademik'),
        ];
    }

    public function toWhatsApp(object $notifiable): string
    {
        $namaSiswa = $this->reportCard->student->nama_siswa;
        $period    = $this->reportCard->period;
        $semester  = "Semester {$period->semester} T.A {$period->tahun_ajaran}";

        return "Assalamu'alaikum Bunda/Ayah,\n\n"
             . "Raport ananda *{$namaSiswa}* untuk *{$semester}* telah tersedia.\n\n"
             . "Silakan login ke portal orang tua untuk melihat dan mengunduh raport.\n\n"
             . "Terima kasih,\n"
             . "IQRA' Creative House";
    }
}
