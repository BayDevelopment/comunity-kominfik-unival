<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengajuan Kerjasama KOMINFIK</title>
</head>

<body
    style="margin:0; padding:0; background-color:#fff7ed; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
        style="background-color:#fff7ed; padding:32px 16px;">
        <tr>
            <td align="center">

                <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                    style="max-width:600px; background-color:#ffffff; border-radius:24px; border:1px solid #ffedd5; overflow:hidden; box-shadow:0 4px 24px rgba(249,115,22,0.08);">

                    <!-- Header gradient -->
                    <tr>
                        <td
                            style="background-color:#f97316; background:linear-gradient(135deg,#f97316,#fbbf24); padding:32px 40px; text-align:center;">

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

                            <h1
                                style="margin:0; color:#ffffff; font-size:22px; font-weight:900; letter-spacing:-0.02em;">
                                Pengajuan Kerjasama Diterima
                            </h1>
                            <p style="margin:6px 0 0; color:#fff7ed; font-size:13px; font-weight:600;">
                                KOMINFIK &middot; Pendaftaran Kerjasama
                            </p>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding:40px;">

                            <p style="margin:0 0 20px; font-size:14px; line-height:1.7; color:#334155;">
                                Halo <strong style="color:#0f172a;">{{ $kerjasama->nama_pic }}</strong>,
                            </p>

                            <p style="margin:0 0 28px; font-size:14px; line-height:1.7; color:#334155;">
                                Terima kasih atas pengajuan kerjasama dari
                                <strong>{{ $kerjasama->nama_instansi }}</strong> kepada KOMINFIK. Pengajuan kamu sudah
                                kami terima dan akan segera ditinjau oleh tim kami.
                            </p>

                            <!-- Badge status -->
                            <table role="presentation" cellpadding="0" cellspacing="0" style="margin-bottom:28px;">
                                <tr>
                                    <td
                                        style="background-color:#fff7ed; border:1px solid #fed7aa; border-radius:9999px; padding:8px 16px;">
                                        <span
                                            style="display:inline-block; width:8px; height:8px; background-color:#f97316; border-radius:9999px; margin-right:8px; vertical-align:middle;"></span>
                                        <span
                                            style="font-size:12px; font-weight:800; color:#c2410c; vertical-align:middle;">Status:
                                            Menunggu Peninjauan</span>
                                    </td>
                                </tr>
                            </table>

                            <!-- Ringkasan data card -->
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                style="background-color:#fffbeb; border:1px solid #ffedd5; border-radius:16px; margin-bottom:28px;">
                                <tr>
                                    <td style="padding:24px;">
                                        <p
                                            style="margin:0 0 16px; font-size:11px; font-weight:900; text-transform:uppercase; letter-spacing:0.08em; color:#ea580c;">
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
                                                    style="padding:8px 0; font-size:13px; color:#78716c; border-top:1px solid #ffedd5;">
                                                    Jenis Instansi</td>
                                                <td
                                                    style="padding:8px 0; font-size:13px; color:#0f172a; font-weight:700; border-top:1px solid #ffedd5; text-transform:capitalize;">
                                                    {{ $kerjasama->jenis_instansi }}</td>
                                            </tr>
                                            <tr>
                                                <td
                                                    style="padding:8px 0; font-size:13px; color:#78716c; border-top:1px solid #ffedd5;">
                                                    Nama PIC</td>
                                                <td
                                                    style="padding:8px 0; font-size:13px; color:#0f172a; font-weight:700; border-top:1px solid #ffedd5;">
                                                    {{ $kerjasama->nama_pic }}</td>
                                            </tr>
                                            @if ($kerjasama->jabatan_pic)
                                                <tr>
                                                    <td
                                                        style="padding:8px 0; font-size:13px; color:#78716c; border-top:1px solid #ffedd5;">
                                                        Jabatan PIC</td>
                                                    <td
                                                        style="padding:8px 0; font-size:13px; color:#0f172a; font-weight:700; border-top:1px solid #ffedd5;">
                                                        {{ $kerjasama->jabatan_pic }}</td>
                                                </tr>
                                            @endif
                                            <tr>
                                                <td
                                                    style="padding:8px 0; font-size:13px; color:#78716c; border-top:1px solid #ffedd5;">
                                                    Email PIC</td>
                                                <td
                                                    style="padding:8px 0; font-size:13px; color:#0f172a; font-weight:700; border-top:1px solid #ffedd5;">
                                                    {{ $kerjasama->email_pic }}</td>
                                            </tr>
                                            <tr>
                                                <td
                                                    style="padding:8px 0; font-size:13px; color:#78716c; border-top:1px solid #ffedd5;">
                                                    No. HP PIC</td>
                                                <td
                                                    style="padding:8px 0; font-size:13px; color:#0f172a; font-weight:700; border-top:1px solid #ffedd5;">
                                                    {{ $kerjasama->no_hp_pic }}</td>
                                            </tr>
                                            @if ($kerjasama->jenis_kerjasama)
                                                <tr>
                                                    <td
                                                        style="padding:8px 0; font-size:13px; color:#78716c; border-top:1px solid #ffedd5;">
                                                        Jenis Kerjasama</td>
                                                    <td
                                                        style="padding:8px 0; font-size:13px; color:#0f172a; font-weight:700; border-top:1px solid #ffedd5;">
                                                        {{ $kerjasama->jenis_kerjasama }}</td>
                                                </tr>
                                            @endif
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            @if ($kerjasama->deskripsi_kerjasama)
                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                    style="background-color:#fafaf9; border:1px solid #f1f5f9; border-radius:16px; margin-bottom:28px;">
                                    <tr>
                                        <td style="padding:20px 24px;">
                                            <p
                                                style="margin:0 0 8px; font-size:11px; font-weight:900; text-transform:uppercase; letter-spacing:0.08em; color:#78716c;">
                                                Deskripsi Kerjasama
                                            </p>
                                            <p style="margin:0; font-size:13px; line-height:1.7; color:#334155;">
                                                {{ \Illuminate\Support\Str::limit($kerjasama->deskripsi_kerjasama, 400) }}
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            @endif

                            <p style="margin:0 0 28px; font-size:14px; line-height:1.7; color:#334155;">
                                Tim kami akan menghubungi PIC melalui email atau nomor HP yang tercantum setelah proses
                                peninjauan selesai. Mohon pantau kotak masuk (dan folder spam) secara berkala.
                            </p>

                            <!-- Button -->
                            <table role="presentation" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td align="center">
                                        <a href="{{ config('app.url') }}"
                                            style="display:inline-block; background-color:#f97316; background:linear-gradient(135deg,#f97316,#fbbf24); color:#ffffff; text-decoration:none; font-size:14px; font-weight:800; padding:14px 32px; border-radius:16px; box-shadow:0 4px 12px rgba(249,115,22,0.3);">
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
