<div style="margin-top: 40px; page-break-inside: avoid;">
    <table style="width: 100%; font-size: 10px; color: #333;">
        <tr>
            @if(isset($waliKelas))
            <td style="width: 50%; text-align: center; vertical-align: top; padding: 0 20px;">
                <p style="margin: 0 0 4px 0;">Medan, {{ now()->translatedFormat('d F Y') }}</p>
                <p style="margin: 0 0 50px 0;">Wali Kelas,</p>
                <p style="margin: 0; border-top: 1px solid #333; display: inline-block; padding-top: 4px; min-width: 150px;">
                    <strong>{{ $waliKelas }}</strong>
                </p>
            </td>
            <td style="width: 50%; text-align: center; vertical-align: top; padding: 0 20px;">
                <p style="margin: 0 0 4px 0;">&nbsp;</p>
                <p style="margin: 0 0 50px 0;">Mengetahui,<br>Kepala Sekolah</p>
                <p style="margin: 0; border-top: 1px solid #333; display: inline-block; padding-top: 4px; min-width: 150px;">
                    <strong>{{ $kepalaSekolah ?? 'Kepala Sekolah' }}</strong>
                </p>
            </td>
            @else
            <td style="width: 50%; text-align: center; vertical-align: top; padding: 0 20px;">
                <p style="margin: 0 0 4px 0;">Medan, {{ now()->translatedFormat('d F Y') }}</p>
                <p style="margin: 0 0 50px 0;">Pembuat Laporan,</p>
                <p style="margin: 0; border-top: 1px solid #333; display: inline-block; padding-top: 4px; min-width: 150px;">
                    <strong>{{ $pembuat ?? 'Admin' }}</strong>
                </p>
            </td>
            <td style="width: 50%; text-align: center; vertical-align: top; padding: 0 20px;">
                <p style="margin: 0 0 4px 0;">&nbsp;</p>
                <p style="margin: 0 0 50px 0;">Mengetahui,<br>Kepala Sekolah</p>
                <p style="margin: 0; border-top: 1px solid #333; display: inline-block; padding-top: 4px; min-width: 150px;">
                    <strong>{{ $kepalaSekolah ?? 'Kepala Sekolah' }}</strong>
                </p>
            </td>
            @endif
        </tr>
    </table>
</div>
