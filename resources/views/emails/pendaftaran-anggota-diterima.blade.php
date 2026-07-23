<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pendaftaran Anggota KOMINFIK</title>
</head>
<body style="margin:0; padding:0; background-color:#fff7ed; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#fff7ed; padding:32px 16px;">
<tr>
<td align="center">

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:600px; background-color:#ffffff; border-radius:24px; border:1px solid #ffedd5; overflow:hidden; box-shadow:0 4px 24px rgba(249,115,22,0.08);">

<!-- Header gradient -->
<tr>
<td style="background:linear-gradient(135deg,#f97316,#fbbf24); padding:32px 40px; text-align:center;">
    <div style="display:inline-flex; align-items:center; justify-content:center; width:56px; height:56px; background-color:rgba(255,255,255,0.2); border-radius:16px; margin-bottom:16px;">
        <span style="font-size:26px; line-height:56px;">&#128075;</span>
    </div>
    <h1 style="margin:0; color:#ffffff; font-size:22px; font-weight:900; letter-spacing:-0.02em;">
        Pendaftaran Berhasil Dikirim
    </h1>
    <p style="margin:6px 0 0; color:#fff7ed; font-size:13px; font-weight:600;">
        KOMINFIK &middot; Pendaftaran Anggota
    </p>
</td>
</tr>

<!-- Body -->
<tr>
<td style="padding:40px;">

    <p style="margin:0 0 20px; font-size:14px; line-height:1.7; color:#334155;">
        Halo <strong style="color:#0f172a;">{{ $pendaftaran->nama }}</strong>,
    </p>

    <p style="margin:0 0 28px; font-size:14px; line-height:1.7; color:#334155;">
        Terima kasih telah mendaftar sebagai anggota <strong>KOMINFIK</strong>. Data kamu sudah kami terima dan sedang dalam proses peninjauan oleh tim kami.
    </p>

    <!-- Badge status -->
    <table role="presentation" cellpadding="0" cellspacing="0" style="margin-bottom:28px;">
    <tr>
    <td style="background-color:#fff7ed; border:1px solid #fed7aa; border-radius:9999px; padding:8px 16px;">
        <span style="display:inline-block; width:8px; height:8px; background-color:#f97316; border-radius:9999px; margin-right:8px; vertical-align:middle;"></span>
        <span style="font-size:12px; font-weight:800; color:#c2410c; vertical-align:middle;">Status: Menunggu Peninjauan</span>
    </td>
    </tr>
    </table>

    <!-- Ringkasan data card -->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#fffbeb; border:1px solid #ffedd5; border-radius:16px; margin-bottom:28px;">
    <tr>
    <td style="padding:24px;">
        <p style="margin:0 0 16px; font-size:11px; font-weight:900; text-transform:uppercase; letter-spacing:0.08em; color:#ea580c;">
            Ringkasan Data Pendaftar
        </p>

        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td style="padding:8px 0; font-size:13px; color:#78716c; width:40%;">Nama Lengkap</td>
                <td style="padding:8px 0; font-size:13px; color:#0f172a; font-weight:700;">{{ $pendaftaran->nama }}</td>
            </tr>
            <tr>
                <td style="padding:8px 0; font-size:13px; color:#78716c; border-top:1px solid #ffedd5;">NIM / NIS</td>
                <td style="padding:8px 0; font-size:13px; color:#0f172a; font-weight:700; border-top:1px solid #ffedd5;">{{ $pendaftaran->nim_nis }}</td>
            </tr>
            <tr>
                <td style="padding:8px 0; font-size:13px; color:#78716c; border-top:1px solid #ffedd5;">Jenjang</td>
                <td style="padding:8px 0; font-size:13px; color:#0f172a; font-weight:700; border-top:1px solid #ffedd5; text-transform:uppercase;">{{ $pendaftaran->jenjang }}</td>
            </tr>
            <tr>
                <td style="padding:8px 0; font-size:13px; color:#78716c; border-top:1px solid #ffedd5;">Asal Instansi</td>
                <td style="padding:8px 0; font-size:13px; color:#0f172a; font-weight:700; border-top:1px solid #ffedd5;">{{ $pendaftaran->asal_instansi }}</td>
            </tr>
            <tr>
                <td style="padding:8px 0; font-size:13px; color:#78716c; border-top:1px solid #ffedd5;">Email</td>
                <td style="padding:8px 0; font-size:13px; color:#0f172a; font-weight:700; border-top:1px solid #ffedd5;">{{ $pendaftaran->email }}</td>
            </tr>
            <tr>
                <td style="padding:8px 0; font-size:13px; color:#78716c; border-top:1px solid #ffedd5;">No. Telepon</td>
                <td style="padding:8px 0; font-size:13px; color:#0f172a; font-weight:700; border-top:1px solid #ffedd5;">{{ $pendaftaran->no_telepon }}</td>
            </tr>
        </table>
    </td>
    </tr>
    </table>

    <p style="margin:0 0 28px; font-size:14px; line-height:1.7; color:#334155;">
        Tim kami akan menghubungi kamu melalui email ini setelah proses seleksi selesai. Mohon pantau kotak masuk (dan folder spam) secara berkala.
    </p>

    <!-- Button -->
    <table role="presentation" cellpadding="0" cellspacing="0" width="100%">
    <tr>
    <td align="center">
        <a href="{{ config('app.url') }}"
           style="display:inline-block; background:linear-gradient(135deg,#f97316,#fbbf24); color:#ffffff; text-decoration:none; font-size:14px; font-weight:800; padding:14px 32px; border-radius:16px; box-shadow:0 4px 12px rgba(249,115,22,0.3);">
            Kunjungi Website Kami
        </a>
    </td>
    </tr>
    </table>

</td>
</tr>

<!-- Footer -->
<tr>
<td style="padding:24px 40px; background-color:#fafaf9; border-top:1px solid #f1f5f9; text-align:center;">
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
