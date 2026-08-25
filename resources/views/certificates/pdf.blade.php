<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        /* SEMENTARA DIMATIKAN untuk tes diagnosis -- font Poppins gagal load
           karena storage/app/public/fonts/ tidak ada di server.
        @font-face {
            font-family: 'Poppins';
            src: url('{{ storage_path('app/public/fonts/Poppins-Regular.ttf') }}');
            font-weight: normal;
        }

        @font-face {
            font-family: 'Poppins';
            src: url('{{ storage_path('app/public/fonts/Poppins-SemiBold.ttf') }}');
            font-weight: 600;
        }
        */

        @page {
            margin: 0px;
            size: {{ $certificate->template->width ?? 842 }}px {{ $certificate->template->height ?? 595 }}px landscape;
        }

        html, body {
            margin: 0;
            padding: 0;
            font-family: 'DejaVu Sans', 'Helvetica', sans-serif;
            color: #2d2a26;
            background-color: #ffffff;
        }

        /* Container utama: width & height EKSPLISIT (angka pasti, bukan %).
           Ini kunci semua perhitungan width di bawahnya supaya benar. */
        .cert-page {
            position: relative;
            box-sizing: border-box;
            overflow: hidden;
            background: linear-gradient(180deg, #fff7ed 0%, #ffffff 22%, #ffffff 78%, #fff7ed 100%);
        }

        .cert-topbar {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            background: linear-gradient(90deg, #0ea5e9 0%, #f97316 55%, #fbbf24 100%);
            z-index: 3;
        }

        /* Border & Shapes (posisi dekoratif, tetap absolute - aman karena hanya pakai top/left/right/bottom+px) */
        .cert-border-outer { position: absolute; border: 2px solid #f97316; z-index: 3; box-sizing: border-box; }
        .cert-border-inner { position: absolute; border: 1px solid #fdba74; z-index: 3; box-sizing: border-box; }
        .cert-border-dashed { position: absolute; border: 1px dashed #fed7aa; z-index: 3; box-sizing: border-box; }
        .corner { position: absolute; z-index: 3; box-sizing: border-box; }

        .cert-watermark {
            position: absolute;
            top: 48%;
            left: 50%;
            transform: translate(-50%, -50%);
            object-fit: contain;
            opacity: 0.12;
            z-index: 1;
        }

        .cert-shape-1 { position: absolute; border-radius: 50%; background: #fed7aa; opacity: 0.25; z-index: 1; }
        .cert-shape-2 { position: absolute; border-radius: 50%; background: #ffedd5; opacity: 0.3; z-index: 1; }

        /* ======================================================================
           PERBAIKAN UTAMA: cert-content BUKAN absolute lagi, tapi normal block
           yang diposisikan pakai padding-top. Karena ini normal flow (bukan
           absolute), semua child dengan width:Npx + margin:0 auto akan
           DIHITUNG BENAR oleh dompdf -- ini yang paling reliable untuk
           wrap teks + center horizontal.
           ====================================================================== */
        .cert-content {
            position: relative;
            z-index: 2;
            text-align: center;
            width: 100%;
            box-sizing: border-box;
        }

        .cert-org-wrap { display: block; }
        .cert-org { font-weight: 600; text-transform: uppercase; color: #ea580c; }
        .cert-diamond { display: inline-block; transform: rotate(45deg); background: #f97316; }

        .cert-title { font-weight: 600; color: #1c1917; text-align: center; }
        .cert-title-accent { margin: 0 auto; background: linear-gradient(90deg, #0ea5e9, #f97316); }

        /* PERBAIKAN DOMPDF: dompdf tidak reliable menghitung div block +
           width + margin:auto (terbukti dari test: browser render benar,
           dompdf render salah). Solusi paling stabil di dompdf: pakai
           TABLE untuk centering + fixed width. Dompdf punya algoritma
           table-layout yang jauh lebih akurat daripada box model biasa. */
        .cert-text-wrap-table {
            margin: 0 auto;
            border-collapse: collapse;
            table-layout: fixed;
        }
        .cert-text-wrap-table td {
            text-align: center;
            word-wrap: break-word;
            overflow-wrap: break-word;
            word-break: break-word;
        }

        .cert-subtitle { color: #57534e; line-height: 1.4; }
        .cert-given-to { text-transform: uppercase; color: #a8a29e; text-align: center; letter-spacing: 1px; }
        .cert-recipient { font-weight: 600; color: #ea580c; line-height: 1.15; }
        .cert-program-label { text-transform: uppercase; color: #a8a29e; text-align: center; letter-spacing: 1px; }
        .cert-program { font-weight: 600; color: #1c1917; }

        /* BAGIAN TANDA TANGAN (posisi dekoratif tetap, aman) */
        .cert-signature {
            position: absolute;
            text-align: center;
            z-index: 2;
        }
        .sign-image { display: block; margin: 0 auto; object-fit: contain; }
        .sign-line { border: none; border-top: 1px solid #1c1917; }
        .sign-name { font-weight: 600; color: #1c1917; }
        .sign-role { text-transform: uppercase; color: #a8a29e; }

        /* FOOTER */
        .cert-footer-center {
            position: absolute;
            left: 0;
            z-index: 2;
            text-align: center;
        }
        .cert-date, .cert-number {
            display: block;
            margin: 0 auto;
            text-align: center;
        }
        .cert-date { color: #78716c; }
        .cert-number { color: #a8a29e; }

        /* STEMPEL */
        .cert-seal { position: absolute; z-index: 2; text-align: center; }
        .cert-seal-ring-outer { border-radius: 50%; border: 2px solid #f97316; display: table; }
        .cert-seal-ring-outer-cell { display: table-cell; vertical-align: middle; text-align: center; }
        .cert-seal-ring-inner { border-radius: 50%; border: 1px dashed #fbbf24; display: table; margin: 0 auto; }
        .cert-seal-ring-inner-cell { display: table-cell; vertical-align: middle; text-align: center; color: #ea580c; font-weight: 600; }
    </style>
</head>

<body>

    @php
        $baseW = 842;
        $baseH = 595;
        $w = $certificate->template->width ?? $baseW;
        $h = $certificate->template->height ?? $baseH;
        $scale = min($w / $baseW, $h / $baseH);

        $sc = fn($px) => (int) round($px * $scale);

        $bulanId = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        $issuedDate = '';
        if ($certificate->issued_at) {
            try {
                $d = \Carbon\Carbon::parse($certificate->issued_at);
                $issuedDate = $d->day . ' ' . $bulanId[$d->month] . ' ' . $d->year;
            } catch (\Exception $e) {
                $issuedDate = $certificate->issued_at;
            }
        }

        $programName = $certificate->event_name ?? $certificate->course_name;
        $orgName = $certificate->metadata['org_name'] ?? config('app.name', 'Sertifikat Digital');

        $subtitle = $certificate->description ?:
            'Diberikan atas partisipasi dan penyelesaian seluruh rangkaian program ' . ($programName ?? '') .
            ($certificate->issued_at ? ' ' . \Carbon\Carbon::parse($certificate->issued_at)->year : '');

        $subtitleLength = strlen($subtitle ?? '');
        $subtitleFontSize = 11;
        if ($subtitleLength > 160) {
            $subtitleFontSize = 9;
        } elseif ($subtitleLength > 100) {
            $subtitleFontSize = 10;
        }

        $signRole = $certificate->signatory_role;
        $signName = $certificate->signatory_name;

        $nameLength = strlen($certificate->recipient_name ?? '');
        $recipientFontSize = 34;
        if ($nameLength > 35) {
            $recipientFontSize = 22;
        } elseif ($nameLength > 25) {
            $recipientFontSize = 26;
        } elseif ($nameLength > 20) {
            $recipientFontSize = 30;
        }

        // Konversi gambar lokal ke Base64 (TTD & watermark)
        $imageToBase64 = function ($path) {
            if ($path && file_exists($path)) {
                $type = pathinfo($path, PATHINFO_EXTENSION);
                $data = file_get_contents($path);
                return 'data:image/' . $type . ';base64,' . base64_encode($data);
            }
            return null;
        };

        $signatureImagePath = $certificate->signatory_signature_path
            ? storage_path('app/public/' . $certificate->signatory_signature_path)
            : null;
        $signatureBase64 = $imageToBase64($signatureImagePath);

        $backgroundImagePath = $certificate->template && $certificate->template->background_image
            ? storage_path('app/public/' . $certificate->template->background_image)
            : null;
        $backgroundBase64 = $imageToBase64($backgroundImagePath);

        // Width teks (dalam px, sudah discale), dipakai untuk subtitle/recipient/program
        $subtitleW = $sc(600);
        $recipientW = $sc(650);
        $programW = $sc(600);
    @endphp

    <div class="cert-page" style="width:{{ $w }}px; height:{{ $h }}px;">
        <div class="cert-topbar" style="height:{{ $sc(10) }}px;"></div>

        @if ($backgroundBase64)
            <img class="cert-watermark" style="height:{{ $sc(220) }}px;" src="{{ $backgroundBase64 }}">
        @else
            <div class="cert-shape-1" style="top:{{ $sc(-90) }}px; left:{{ $sc(-110) }}px; width:{{ $sc(440) }}px; height:{{ $sc(440) }}px;"></div>
            <div class="cert-shape-2" style="bottom:{{ $sc(-130) }}px; right:{{ $sc(-90) }}px; width:{{ $sc(500) }}px; height:{{ $sc(500) }}px;"></div>
        @endif

        <div class="cert-border-outer" style="top:{{ $sc(20) }}px; left:{{ $sc(20) }}px; right:{{ $sc(20) }}px; bottom:{{ $sc(20) }}px; border-width:{{ $sc(2) }}px;"></div>
        <div class="cert-border-inner" style="top:{{ $sc(26) }}px; left:{{ $sc(26) }}px; right:{{ $sc(26) }}px; bottom:{{ $sc(26) }}px;"></div>
        <div class="cert-border-dashed" style="top:{{ $sc(32) }}px; left:{{ $sc(32) }}px; right:{{ $sc(32) }}px; bottom:{{ $sc(32) }}px;"></div>

        <div class="corner" style="top:{{ $sc(38) }}px; left:{{ $sc(38) }}px; width:{{ $sc(45) }}px; height:{{ $sc(45) }}px; border-top:{{ $sc(3) }}px solid #fbbf24; border-left:{{ $sc(3) }}px solid #fbbf24;"></div>
        <div class="corner" style="top:{{ $sc(38) }}px; right:{{ $sc(38) }}px; width:{{ $sc(45) }}px; height:{{ $sc(45) }}px; border-top:{{ $sc(3) }}px solid #fbbf24; border-right:{{ $sc(3) }}px solid #fbbf24;"></div>
        <div class="corner" style="bottom:{{ $sc(38) }}px; left:{{ $sc(38) }}px; width:{{ $sc(45) }}px; height:{{ $sc(45) }}px; border-bottom:{{ $sc(3) }}px solid #fbbf24; border-left:{{ $sc(3) }}px solid #fbbf24;"></div>
        <div class="corner" style="bottom:{{ $sc(38) }}px; right:{{ $sc(38) }}px; width:{{ $sc(45) }}px; height:{{ $sc(45) }}px; border-bottom:{{ $sc(3) }}px solid #fbbf24; border-right:{{ $sc(3) }}px solid #fbbf24;"></div>

        <!-- KONTEN: sekarang normal flow (bukan absolute), didorong turun pakai padding-top -->
        <div class="cert-content" style="padding-top:{{ $sc(80) }}px;">

            <div class="cert-org-wrap" style="margin-bottom:{{ $sc(15) }}px;">
                <span class="cert-diamond" style="width:{{ $sc(5) }}px; height:{{ $sc(5) }}px; margin-right:{{ $sc(8) }}px;"></span>
                <span class="cert-org" style="font-size:{{ $sc(12) }}px; letter-spacing:1px;">{{ strtoupper($orgName) }}</span>
                <span class="cert-diamond" style="width:{{ $sc(5) }}px; height:{{ $sc(5) }}px; margin-left:{{ $sc(8) }}px;"></span>
            </div>

            <div class="cert-title" style="font-size:{{ $sc(38) }}px; margin-bottom:{{ $sc(8) }}px;">
                Sertifikat Apresiasi
            </div>

            <table style="width:{{ $sc(70) }}px; margin:0 auto {{ $sc(16) }}px auto; border-collapse:collapse;">
                <tr><td style="height:{{ $sc(3) }}px; background:linear-gradient(90deg, #0ea5e9, #f97316); font-size:0; line-height:0;">&nbsp;</td></tr>
            </table>

            @if ($subtitle)
                <table class="cert-text-wrap-table" style="width:{{ $subtitleW }}px; margin-bottom:{{ $sc(16) }}px;">
                    <tr>
                        <td class="cert-subtitle" style="font-size:{{ $sc($subtitleFontSize) }}px;">
                            {{ $subtitle }}
                        </td>
                    </tr>
                </table>
            @endif

            <div class="cert-given-to" style="font-size:{{ $sc(10) }}px; margin-bottom:{{ $sc(6) }}px;">
                DIBERIKAN KEPADA
            </div>

            <table class="cert-text-wrap-table" style="width:{{ $recipientW }}px; margin-bottom:{{ $sc(16) }}px;">
                <tr>
                    <td class="cert-recipient" style="font-size:{{ $sc($recipientFontSize) }}px;">
                        {{ $certificate->recipient_name }}
                    </td>
                </tr>
            </table>

            @if ($programName)
                <div class="cert-program-label" style="font-size:{{ $sc(9) }}px; margin-bottom:{{ $sc(4) }}px;">
                    PROGRAM
                </div>
                <table class="cert-text-wrap-table" style="width:{{ $programW }}px;">
                    <tr>
                        <td class="cert-program" style="font-size:{{ $sc(16) }}px;">
                            {{ $programName }}
                        </td>
                    </tr>
                </table>
            @endif
        </div>

        <div class="cert-seal" style="bottom:{{ $sc(55) }}px; left:{{ $sc(60) }}px; width:{{ $sc(70) }}px; height:{{ $sc(70) }}px;">
            <div class="cert-seal-ring-outer" style="width:{{ $sc(70) }}px; height:{{ $sc(70) }}px;">
                <div class="cert-seal-ring-outer-cell" style="width:{{ $sc(70) }}px; height:{{ $sc(70) }}px;">
                    <div class="cert-seal-ring-inner" style="width:{{ $sc(56) }}px; height:{{ $sc(56) }}px;">
                        <div class="cert-seal-ring-inner-cell" style="width:{{ $sc(56) }}px; height:{{ $sc(56) }}px; font-size:{{ $sc(7) }}px; line-height:1.3;">
                            RESMI<br>{{ $certificate->issued_at ? \Carbon\Carbon::parse($certificate->issued_at)->year : date('Y') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if ($signName || $signatureBase64)
            <div class="cert-signature" style="bottom:{{ $sc(50) }}px; right:{{ $sc(60) }}px; width:{{ $sc(200) }}px;">
                @if ($signatureBase64)
                    <img class="sign-image" src="{{ $signatureBase64 }}" style="height:{{ $sc(55) }}px; max-width:{{ $sc(190) }}px; margin-bottom:{{ $sc(2) }}px;">
                @else
                    <div style="height:{{ $sc(55) }}px;"></div>
                @endif
                <div class="sign-line" style="margin-bottom:{{ $sc(4) }}px;"></div>
                @if ($signName)
                    <div class="sign-name" style="font-size:{{ $sc(11) }}px;">{{ $signName }}</div>
                @endif
                @if ($signRole)
                    <div class="sign-role" style="font-size:{{ $sc(8) }}px; margin-top:{{ $sc(2) }}px;">
                        {{ $signRole }}
                    </div>
                @endif
            </div>
        @endif

        <div class="cert-footer-center" style="bottom:{{ $sc(45) }}px; width:{{ $w }}px;">
            @if ($issuedDate)
                <span class="cert-date" style="font-size:{{ $sc(9) }}px; margin-bottom:{{ $sc(3) }}px;">Diterbitkan pada {{ $issuedDate }}</span>
            @endif
            <span class="cert-number" style="font-size:{{ $sc(8) }}px;">No. {{ $certificate->certificate_number }}</span>
        </div>
    </div>

</body>

</html>