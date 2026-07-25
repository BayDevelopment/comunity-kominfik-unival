<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Bergabung - KOMINFIK</title>

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
                    style="max-width:600px; background-color:#ffffff; border-radius:24px; border:1px solid #dcfce7; overflow:hidden; box-shadow:0 4px 24px rgba(34,197,94,0.08);">

                    <!-- Header gradient -->
                    <tr>
                        <td class="email-header"
                            style="background:linear-gradient(135deg,#16a34a,#4ade80); padding:32px 40px; text-align:center;">
                            <div
                                style="display:inline-flex; align-items:center; justify-content:center; width:56px; height:56px; background-color:rgba(255,255,255,0.2); border-radius:16px; margin-bottom:16px;">
                                <span style="font-size:26px; line-height:56px;">&#127881;</span>
                            </div>
                            <h1 class="email-h1"
                                style="margin:0; color:#ffffff; font-size:22px; font-weight:900; letter-spacing:-0.02em;">
                                Selamat Bergabung!
                            </h1>
                            <p style="margin:6px 0 0; color:#f0fdf4; font-size:13px; font-weight:600;">
                                KOMINFIK &middot; Pendaftaran Anggota
                            </p>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td class="email-body" style="padding:40px;">

                            <p style="margin:0 0 20px; font-size:14px; line-height:1.7; color:#334155;">
                                Halo <strong style="color:#0f172a;">{{ $pendaftaran->nama }}</strong>,
                            </p>

                            <p style="margin:0 0 28px; font-size:14px; line-height:1.7; color:#334155;">
                                Selamat! Setelah melalui proses peninjauan, kami dengan senang hati mengumumkan bahwa
                                kamu <strong>resmi diterima</strong> sebagai anggota <strong>KOMINFIK</strong>. Kami
                                menantikan kontribusi dan kolaborasi bersamamu ke depannya.
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
                                            Diterima sebagai Anggota</span>
                                    </td>
                                </tr>
                            </table>

                            <!-- Ringkasan data card -->
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                style="background-color:#f8fafc; border:1px solid #e2e8f0; border-radius:16px; margin-bottom:28px;">
                                <tr>
                                    <td class="email-summary-card" style="padding:24px;">
                                        <p
                                            style="margin:0 0 16px; font-size:11px; font-weight:900; text-transform:uppercase; letter-spacing:0.08em; color:#15803d;">
                                            Detail Keanggotaan
                                        </p>

                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="padding:8px 0; font-size:13px; color:#78716c; width:40%;">
                                                    Nama Lengkap</td>
                                                <td
                                                    style="padding:8px 0; font-size:13px; color:#0f172a; font-weight:700;">
                                                    {{ $pendaftaran->nama }}</td>
                                            </tr>
                                            <tr>
                                                <td
                                                    style="padding:8px 0; font-size:13px; color:#78716c; border-top:1px solid #e2e8f0;">
                                                    Email</td>
                                                <td
                                                    style="padding:8px 0; font-size:13px; color:#0f172a; font-weight:700; border-top:1px solid #e2e8f0;">
                                                    {{ $pendaftaran->email }}</td>
                                            </tr>
                                            @if ($anggota->jabatan)
                                                <tr>
                                                    <td
                                                        style="padding:8px 0; font-size:13px; color:#78716c; border-top:1px solid #e2e8f0;">
                                                        Jabatan</td>
                                                    <td
                                                        style="padding:8px 0; font-size:13px; color:#0f172a; font-weight:700; border-top:1px solid #e2e8f0;">
                                                        {{ $anggota->jabatan }}</td>
                                                </tr>
                                            @endif
                                            @if ($anggota->divisi)
                                                <tr>
                                                    <td
                                                        style="padding:8px 0; font-size:13px; color:#78716c; border-top:1px solid #e2e8f0;">
                                                        Divisi</td>
                                                    <td
                                                        style="padding:8px 0; font-size:13px; color:#0f172a; font-weight:700; border-top:1px solid #e2e8f0;">
                                                        {{ $anggota->divisi }}</td>
                                                </tr>
                                            @endif
                                            <tr>
                                                <td
                                                    style="padding:8px 0; font-size:13px; color:#78716c; border-top:1px solid #e2e8f0;">
                                                    Tanggal Bergabung</td>
                                                <td
                                                    style="padding:8px 0; font-size:13px; color:#0f172a; font-weight:700; border-top:1px solid #e2e8f0;">
                                                    {{ \Carbon\Carbon::parse($anggota->tanggal_bergabung)->translatedFormat('d F Y') }}
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:0 0 28px; font-size:14px; line-height:1.7; color:#334155;">
                                Informasi lebih lanjut mengenai kegiatan dan agenda organisasi akan disampaikan melalui
                                email ini atau kanal komunikasi resmi KOMINFIK. Jangan ragu untuk menghubungi kami jika
                                ada pertanyaan.
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
