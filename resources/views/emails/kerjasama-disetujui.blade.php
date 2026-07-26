<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengajuan Kerjasama Disetujui</title>

    <style>
        @media only screen and (max-width: 600px) {
            .email-outer-padding {
                padding: 16px 12px !important;
            }

            .email-header {
                padding: 24px 20px !important;
            }

            .email-body {
                padding: 24px !important;
            }

            .email-h1 {
                font-size: 19px !important;
            }

            .email-summary-card {
                padding: 16px !important;
            }

            .email-cta-btn {
                padding: 12px 24px !important;
                font-size: 13px !important;
            }
        }
    </style>

</head>

<body
    style="margin:0; padding:0; background-color:#f0fdf4; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f0fdf4;">
        <tr>
            <td class="email-outer-padding" align="center" style="padding:32px 16px;">

                <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                    style="max-width:600px; background-color:#ffffff; border-radius:24px; border:1px solid #dcfce7; overflow:hidden; box-shadow:0 4px 24px rgba(22,163,74,0.08);">

                    <!-- Header gradient -->
                    <tr>
                        <td class="email-header"
                            style="background:linear-gradient(135deg,#16a34a,#4ade80); padding:32px 40px; text-align:center;">

                            <table role="presentation" cellpadding="0" cellspacing="0" align="center"
                                style="margin:0 auto 16px;">
                                <tr>
                                    <td width="56" height="56" align="center" valign="middle"
                                        style="background-color:rgba(255,255,255,0.2); border-radius:16px;">
                                        <img src="{{ asset('images/logo-kominfik.png') }}" alt="KOMINFIK" width="36"
                                            height="36" style="display:block; object-fit:contain;">
                                    </td>
                                </tr>
                            </table>

                            <h1 class="email-h1"
                                style="margin:0; color:#ffffff; font-size:22px; font-weight:900; letter-spacing:-0.02em;">
                                Pengajuan Kerjasama Disetujui
                            </h1>
                            <p style="margin:6px 0 0; color:#f0fdf4; font-size:13px; font-weight:600;">
                                KOMINFIK &middot; Pendaftaran Kerjasama
                            </p>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td class="email-body" style="padding:40px;">

                            <p style="margin:0 0 20px; font-size:14px; line-height:1.7; color:#334155;">
                                Halo <strong style="color:#0f172a;">{{ $kerjasama->nama_pic }}</strong>,
                            </p>

                            <p style="margin:0 0 28px; font-size:14px; line-height:1.7; color:#334155;">
                                Selamat! Pengajuan kerjasama dari <strong>{{ $kerjasama->nama_instansi }}</strong>
                                telah <strong style="color:#16a34a;">disetujui</strong> oleh tim KOMINFIK. Kami akan
                                segera menghubungi Anda untuk membahas langkah selanjutnya.
                            </p>

                            <!-- Badge status -->
                            <table role="presentation" cellpadding="0" cellspacing="0" style="margin-bottom:28px;">
                                <tr>
                                    <td
                                        style="background-color:#f0fdf4; border:1px solid #bbf7d0; border-radius:9999px; padding:8px 16px;">
                                        <span
                                            style="display:inline-block; width:8px; height:8px; background-color:#16a34a; border-radius:9999px; margin-right:8px; vertical-align:middle;"></span>
                                        <span
                                            style="font-size:12px; font-weight:800; color:#15803d; vertical-align:middle;">Status:
                                            Disetujui</span>
                                    </td>
                                </tr>
                            </table>

                            <!-- Ringkasan data card -->
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                style="background-color:#f0fdf4; border:1px solid #dcfce7; border-radius:16px; margin-bottom:28px;">
                                <tr>
                                    <td class="email-summary-card" style="padding:24px;">
                                        <p
                                            style="margin:0 0 16px; font-size:11px; font-weight:900; text-transform:uppercase; letter-spacing:0.08em; color:#15803d;">
                                            Ringkasan Pengajuan
                                        </p>

                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="padding:8px 0; font-size:13px; color:#78716c; width:40%;">
                                                    Nama Instansi</td>
                                                <td
                                                    style="padding:8px 0; font-size:13px; color:#0f172a; font-weight:700;">
                                                    {{ $kerjasama->nama_instansi }}</td>
                                            </tr>
                                            <tr>
                                                <td
                                                    style="padding:8px 0; font-size:13px; color:#78716c; border-top:1px solid #dcfce7;">
                                                    PIC</td>
                                                <td
                                                    style="padding:8px 0; font-size:13px; color:#0f172a; font-weight:700; border-top:1px solid #dcfce7;">
                                                    {{ $kerjasama->nama_pic }}</td>
                                            </tr>
                                            <tr>
                                                <td
                                                    style="padding:8px 0; font-size:13px; color:#78716c; border-top:1px solid #dcfce7;">
                                                    Jenis Kerjasama</td>
                                                <td
                                                    style="padding:8px 0; font-size:13px; color:#0f172a; font-weight:700; border-top:1px solid #dcfce7;">
                                                    {{ $kerjasama->jenis_kerjasama ?? '—' }}</td>
                                            </tr>
                                            @if ($kerjasama->catatan_admin)
                                                <tr>
                                                    <td
                                                        style="padding:8px 0; font-size:13px; color:#78716c; border-top:1px solid #dcfce7;">
                                                        Catatan Admin</td>
                                                    <td
                                                        style="padding:8px 0; font-size:13px; color:#0f172a; font-weight:700; border-top:1px solid #dcfce7;">
                                                        {{ $kerjasama->catatan_admin }}</td>
                                                </tr>
                                            @endif
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:0 0 28px; font-size:14px; line-height:1.7; color:#334155;">
                                Jika ada pertanyaan lebih lanjut, jangan ragu untuk menghubungi tim kami.
                            </p>

                            <!-- Button -->
                            <table role="presentation" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td align="center">
                                        <a href="{{ config('app.url') }}" class="email-cta-btn"
                                            style="display:inline-block; background:linear-gradient(135deg,#16a34a,#4ade80); color:#ffffff; text-decoration:none; font-size:14px; font-weight:800; padding:14px 32px; border-radius:16px; box-shadow:0 4px 12px rgba(22,163,74,0.3);">
                                            Kunjungi Website Kami
                                        </a>
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td
                            style="padding:24px 40px; background-color:#fafaf9; border-top:1px solid #f1f5f9; text-align:center;">
                            <p style="margin:0; font-size:12px; color:#94a3b8;">
                                Email ini dikirim otomatis, mohon tidak membalas ke alamat ini.
                            </p>
                            <p style="margin:6px 0 0; font-size:12px; color:#94a3b8;">
                                &copy; {{ date('Y') }} {{ config('app.name') }}. Semua hak dilindungi.
                            </p>
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>

</html>
