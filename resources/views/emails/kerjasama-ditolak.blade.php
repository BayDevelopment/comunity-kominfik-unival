<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informasi Hasil Pengajuan Kerjasama</title>

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
    style="margin:0; padding:0; background-color:#f8fafc; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f8fafc;">
        <tr>
            <td class="email-outer-padding" align="center" style="padding:32px 16px;">

                <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                    style="max-width:600px; background-color:#ffffff; border-radius:24px; border:1px solid #e2e8f0; overflow:hidden; box-shadow:0 4px 24px rgba(71,85,105,0.08);">

                    <!-- Header gradient -->
                    <tr>
                        <td class="email-header"
                            style="background:linear-gradient(135deg,#475569,#94a3b8); padding:32px 40px; text-align:center;">

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
                                Informasi Hasil Pengajuan Kerjasama
                            </h1>
                            <p style="margin:6px 0 0; color:#f1f5f9; font-size:13px; font-weight:600;">
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
                                Terima kasih atas minat <strong>{{ $kerjasama->nama_instansi }}</strong> untuk
                                menjalin kerjasama dengan KOMINFIK. Setelah melalui proses peninjauan, mohon maaf
                                pengajuan kerjasama Anda saat ini <strong>belum dapat kami setujui</strong>.
                            </p>

                            <!-- Badge status -->
                            <table role="presentation" cellpadding="0" cellspacing="0" style="margin-bottom:28px;">
                                <tr>
                                    <td
                                        style="background-color:#f8fafc; border:1px solid #e2e8f0; border-radius:9999px; padding:8px 16px;">
                                        <span
                                            style="display:inline-block; width:8px; height:8px; background-color:#64748b; border-radius:9999px; margin-right:8px; vertical-align:middle;"></span>
                                        <span
                                            style="font-size:12px; font-weight:800; color:#475569; vertical-align:middle;">Status:
                                            Ditolak</span>
                                    </td>
                                </tr>
                            </table>

                            @if ($kerjasama->catatan_admin)
                                <!-- Catatan card -->
                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                    style="background-color:#f8fafc; border:1px solid #e2e8f0; border-radius:16px; margin-bottom:28px;">
                                    <tr>
                                        <td class="email-summary-card" style="padding:24px;">
                                            <p
                                                style="margin:0 0 8px; font-size:11px; font-weight:900; text-transform:uppercase; letter-spacing:0.08em; color:#475569;">
                                                Catatan dari Tim Kami
                                            </p>
                                            <p style="margin:0; font-size:13px; line-height:1.6; color:#334155;">
                                                {{ $kerjasama->catatan_admin }}
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            @endif

                            <p style="margin:0 0 28px; font-size:14px; line-height:1.7; color:#334155;">
                                Kami sangat menghargai minat Anda dan berharap dapat menjalin kerjasama di kesempatan
                                lain. Jangan ragu untuk mengajukan kembali di masa mendatang.
                            </p>

                            <!-- Button -->
                            <table role="presentation" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td align="center">
                                        <a href="{{ config('app.url') }}" class="email-cta-btn"
                                            style="display:inline-block; background:linear-gradient(135deg,#475569,#94a3b8); color:#ffffff; text-decoration:none; font-size:14px; font-weight:800; padding:14px 32px; border-radius:16px; box-shadow:0 4px 12px rgba(71,85,105,0.3);">
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
