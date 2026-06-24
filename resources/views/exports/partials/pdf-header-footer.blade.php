<style>
    @page {
        margin: 120px 40px 80px 40px;
    }
    body {
        font-family: sans-serif;
        font-size: 11px;
        color: #333;
        margin: 0;
        padding: 0;
    }
    .header-container {
        position: fixed;
        top: -100px;
        left: 0;
        right: 0;
        height: 90px;
        border-bottom: 2px solid #3DA746;
    }
    .header-table {
        width: 100%;
        border-collapse: collapse;
    }
    .header-table td {
        vertical-align: middle;
        padding: 0;
    }
    .header-logo {
        width: 60px;
        padding-right: 12px;
    }
    .header-logo-circle {
        width: 50px;
        height: 50px;
        background: #3DA746;
        border-radius: 50%;
        text-align: center;
        line-height: 50px;
        color: #fff;
        font-weight: bold;
        font-size: 18px;
        font-family: sans-serif;
    }
    .header-info {
        line-height: 1.3;
    }
    .header-school {
        font-size: 16px;
        font-weight: bold;
        color: #3DA746;
        margin: 0;
    }
    .header-address {
        font-size: 9px;
        color: #666;
        margin: 0;
    }
    .header-report-title {
        text-align: right;
        font-size: 10px;
        color: #888;
    }
    .footer-container {
        position: fixed;
        bottom: -60px;
        left: 0;
        right: 0;
        height: 50px;
        border-top: 1px solid #ddd;
        padding-top: 8px;
    }
    .footer-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 8px;
        color: #999;
    }
    .footer-table td {
        padding: 0;
        vertical-align: middle;
    }
    .footer-left {
        text-align: left;
    }
    .footer-center {
        text-align: center;
    }
    .footer-right {
        text-align: right;
    }
    .page-number:after {
        content: counter(page);
    }
    .page-total:after {
        content: counter(pages);
    }
</style>

<div class="header-container">
    <table class="header-table">
        <tr>
            <td class="header-logo">
                <div class="header-logo-circle">ICH</div>
            </td>
            <td class="header-info">
                <p class="header-school">TK IQRA' Creative House</p>
                <p class="header-address">Jl. Karya Wisata, Medan Johor, Kota Medan, Sumatera Utara</p>
                <p class="header-address">Email: info@iqracreativehouse.com</p>
            </td>
            <td class="header-report-title">
                <strong style="font-size: 11px; color: #333;">{{ $reportTitle ?? 'Laporan' }}</strong><br>
                Dicetak: {{ now()->translatedFormat('d F Y') }}<br>
                Tahun Ajaran {{ now()->year }}/{{ now()->year + 1 }}
            </td>
        </tr>
    </table>
</div>

<div class="footer-container">
    <table class="footer-table">
        <tr>
            <td class="footer-left" style="width: 33%;">
                <em>Dokumen ini digenerate otomatis oleh sistem IMS ICH</em>
            </td>
            <td class="footer-center" style="width: 34%;">
                TK IQRA' Creative House &mdash; Medan
            </td>
            <td class="footer-right" style="width: 33%;">
                Halaman <span class="page-number"></span> dari <span class="page-total"></span>
            </td>
        </tr>
    </table>
</div>
