<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
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

        @page {
            margin: 0;
            size: {{ $certificate->template->width ?? 842 }}px {{ $certificate->template->height ?? 595 }}px;
        }

        body {
            margin: 0;
            padding: 0;
            width: {{ $certificate->template->width ?? 842 }}px;
            height: {{ $certificate->template->height ?? 595 }}px;
            position: relative;
            font-family: 'Poppins', 'Helvetica', sans-serif;
            color: #2d2a26;
        }

        .cert-page {
            position: relative;
            width: 100%;
            height: 100%;
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

        .cert-border-outer {
            position: absolute;
            border: 2px solid #f97316;
            z-index: 3;
        }

        .cert-border-inner {
            position: absolute;
            border: 1px solid #fdba74;
            z-index: 3;
        }

        .cert-border-dashed {
            position: absolute;
            border: 1px dashed #fed7aa;
            z-index: 3;
        }

        .corner {
            position: absolute;
            z-index: 3;
        }

        .cert-watermark {
            position: absolute;
            top: 46%;
            left: 50%;
            transform: translate(-50%, -50%);
            object-fit: contain;
            opacity: 0.12;
            z-index: 1;
        }

        .cert-shape-1 {
            position: absolute;
            border-radius: 50%;
            background: #fed7aa;
            opacity: 0.25;
            z-index: 1;
        }

        .cert-shape-2 {
            position: absolute;
            border-radius: 50%;
            background: #ffedd5;
            opacity: 0.3;
            z-index: 1;
        }

        .cert-content {
            position: relative;
            z-index: 2;
            width: 100%;
            text-align: center;
            box-sizing: border-box;
        }

        .cert-org {
            font-weight: 600;
            text-transform: uppercase;
            color: #ea580c;
        }

        .cert-org-wrap {
            display: block;
        }

        .cert-diamond {
            display: inline-block;
            transform: rotate(45deg);
            background: #f97316;
        }

        .cert-title {
            font-weight: 600;
            color: #1c1917;
        }

        .cert-title-accent {
            margin: 0 auto;
            background: linear-gradient(90deg, #0ea5e9, #f97316);
        }

        .cert-subtitle {
            color: #57534e;
            line-height: 1.6;
        }

        .cert-given-to {
            text-transform: uppercase;
            color: #a8a29e;
        }

        .cert-recipient {
            font-weight: 600;
            color: #ea580c;
            line-height: 1.1;
        }

        .cert-program-label {
            text-transform: uppercase;
            color: #a8a29e;
        }

        .cert-program {
            font-weight: 600;
            color: #1c1917;
        }

        .cert-signature {
            position: absolute;
            text-align: center;
            z-index: 2;
        }

        .sign-image {
            display: block;
            margin: 0 auto;
            object-fit: contain;
        }

        .sign-line {
            border: none;
            border-top: 1px solid #1c1917;
        }

        .sign-name {
            font-weight: 600;
            color: #1c1917;
        }

        .sign-role {
            text-transform: uppercase;
            color: #a8a29e;
        }

        .cert-date {
            position: absolute;
            left: 0;
            right: 0;
            text-align: center;
            color: #78716c;
            z-index: 2;
        }

        .cert-number {
            position: absolute;
            left: 0;
            right: 0;
            text-align: center;
            letter-spacing: 2px;
            color: #d6d3d1;
            z-index: 2;
        }

        .cert-seal {
            position: absolute;
            z-index: 2;
            text-align: center;
        }

        .cert-seal-ring-outer {
            border-radius: 50%;
            border: 2px solid #f97316;
            display: table;
        }

        .cert-seal-ring-outer-cell {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
        }

        .cert-seal-ring-inner {
            border-radius: 50%;
            border: 1px dashed #fbbf24;
            display: table;
            margin: 0 auto;
        }

        .cert-seal-ring-inner-cell {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
            color: #ea580c;
            font-weight: 600;
        }
    </style>
</head>

<body>

    @php
        $baseW = 842;
        $baseH = 595;
        $w = $certificate->template->width ?? $baseW;
        $h = $certificate->template->height ?? $baseH;
        $scale = min($w / $baseW, $h / $baseH);
        $sc = fn($px) => round($px * $scale, 1);

        $bulanId = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
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

        // 'program' relation tidak ada di model, dan sekarang program name
        // disimpan langsung sebagai kolom string event_name / course_name.
        $programName = $certificate->event_name ?? $certificate->course_name;

        $orgName = $certificate->metadata['org_name'] ?? config('app.name', 'Sertifikat Digital');

        $subtitle =
            $certificate->description ?:
            'Atas partisipasi dan penyelesaian program ' .
                ($programName ?? '') .
                ($certificate->issued_at ? ' ' . \Carbon\Carbon::parse($certificate->issued_at)->year : '');

        // signed_by tidak ada di fillable. Nama penandatangan = signatory_name,
        // jabatan/role penandatangan = signatory_role.
        $signRole = $certificate->signatory_role;
        $signName = $certificate->signatory_name;
        // Akses langsung ke filesystem (storage_path), tidak lewat symlink
        // public/storage — supaya tidak bergantung pada `php artisan storage:link`
        // yang sering belum/gagal dijalankan di hosting.
        $signatureImagePath = $certificate->signatory_signature_path
            ? storage_path('app/public/' . $certificate->signatory_signature_path)
            : null;
        $signatureImageExists = $signatureImagePath && file_exists($signatureImagePath);

        $backgroundImagePath =
            $certificate->template && $certificate->template->background_image
                ? storage_path('app/public/' . $certificate->template->background_image)
                : null;
        $backgroundImageExists = $backgroundImagePath && file_exists($backgroundImagePath);
    @endphp

    <div class="cert-page">
        <div class="cert-topbar" style="height:{{ $sc(12) }}px;"></div>

        @if ($backgroundImageExists)
            <img class="cert-watermark" style="height:{{ $sc(230) }}px;" src="{{ $backgroundImagePath }}">
        @else
            <div class="cert-shape-1"
                style="top:{{ $sc(-90) }}px; left:{{ $sc(-110) }}px; width:{{ $sc(440) }}px; height:{{ $sc(440) }}px;">
            </div>
            <div class="cert-shape-2"
                style="bottom:{{ $sc(-130) }}px; right:{{ $sc(-90) }}px; width:{{ $sc(500) }}px; height:{{ $sc(500) }}px;">
            </div>
        @endif

        <div class="cert-border-outer"
            style="top:{{ $sc(20) }}px; left:{{ $sc(20) }}px; right:{{ $sc(20) }}px; bottom:{{ $sc(20) }}px; border-width:{{ $sc(2) }}px;">
        </div>
        <div class="cert-border-inner"
            style="top:{{ $sc(28) }}px; left:{{ $sc(28) }}px; right:{{ $sc(28) }}px; bottom:{{ $sc(28) }}px;">
        </div>
        <div class="cert-border-dashed"
            style="top:{{ $sc(34) }}px; left:{{ $sc(34) }}px; right:{{ $sc(34) }}px; bottom:{{ $sc(34) }}px;">
        </div>

        <div class="corner"
            style="top:{{ $sc(42) }}px; left:{{ $sc(42) }}px; width:{{ $sc(50) }}px; height:{{ $sc(50) }}px; border-top:{{ $sc(3) }}px solid #fbbf24; border-left:{{ $sc(3) }}px solid #fbbf24;">
        </div>
        <div class="corner"
            style="top:{{ $sc(42) }}px; right:{{ $sc(42) }}px; width:{{ $sc(50) }}px; height:{{ $sc(50) }}px; border-top:{{ $sc(3) }}px solid #fbbf24; border-right:{{ $sc(3) }}px solid #fbbf24;">
        </div>
        <div class="corner"
            style="bottom:{{ $sc(42) }}px; left:{{ $sc(42) }}px; width:{{ $sc(50) }}px; height:{{ $sc(50) }}px; border-bottom:{{ $sc(3) }}px solid #fbbf24; border-left:{{ $sc(3) }}px solid #fbbf24;">
        </div>
        <div class="corner"
            style="bottom:{{ $sc(42) }}px; right:{{ $sc(42) }}px; width:{{ $sc(50) }}px; height:{{ $sc(50) }}px; border-bottom:{{ $sc(3) }}px solid #fbbf24; border-right:{{ $sc(3) }}px solid #fbbf24;">
        </div>

        <div class="cert-content" style="padding-top:{{ $sc(45) }}px;">
            <div class="cert-org-wrap" style="margin-bottom:{{ $sc(12) }}px;">
                <span class="cert-diamond"
                    style="width:{{ $sc(5) }}px; height:{{ $sc(5) }}px; margin-right:{{ $sc(10) }}px;"></span>
                <span class="cert-org"
                    style="font-size:{{ $sc(13) }}px; letter-spacing:{{ $sc(5) }}px;">{{ strtoupper($orgName) }}</span>
                <span class="cert-diamond"
                    style="width:{{ $sc(5) }}px; height:{{ $sc(5) }}px; margin-left:{{ $sc(10) }}px;"></span>
            </div>

            <div class="cert-title"
                style="font-size:{{ $sc(40) }}px; letter-spacing:{{ $sc(1) }}px; margin-bottom:{{ $sc(6) }}px; line-height:1.1;">
                Sertifikat Apresiasi
            </div>

            <div class="cert-title-accent"
                style="width:{{ $sc(80) }}px; height:{{ $sc(3) }}px; margin-bottom:{{ $sc(12) }}px;">
            </div>

            @if ($subtitle)
                <div class="cert-subtitle"
                    style="font-size:{{ $sc(13) }}px; margin-bottom:{{ $sc(10) }}px; padding:0 {{ $sc(90) }}px; line-height:1.4;">
                    {{ $subtitle }}
                </div>
            @endif

            <div class="cert-given-to"
                style="font-size:{{ $sc(12) }}px; letter-spacing:{{ $sc(2) }}px; margin-bottom:{{ $sc(8) }}px;">
                Diberikan kepada
            </div>

            <div class="cert-recipient"
                style="font-size:{{ $sc(40) }}px; margin-bottom:{{ $sc(16) }}px; line-height:1.1;">
                {{ $certificate->recipient_name }}
            </div>

            @if ($programName)
                <div class="cert-program-label"
                    style="font-size:{{ $sc(11) }}px; letter-spacing:{{ $sc(2) }}px; margin-bottom:{{ $sc(6) }}px;">
                    Program
                </div>
                <div class="cert-program" style="font-size:{{ $sc(19) }}px;">
                    {{ $programName }}
                </div>
            @endif
        </div>

        <div class="cert-seal"
            style="bottom:{{ $sc(50) }}px; left:{{ $sc(70) }}px; width:{{ $sc(90) }}px; height:{{ $sc(90) }}px;">
            <div class="cert-seal-ring-outer" style="width:{{ $sc(90) }}px; height:{{ $sc(90) }}px;">
                <div class="cert-seal-ring-outer-cell"
                    style="width:{{ $sc(90) }}px; height:{{ $sc(90) }}px;">
                    <div class="cert-seal-ring-inner"
                        style="width:{{ $sc(74) }}px; height:{{ $sc(74) }}px;">
                        <div class="cert-seal-ring-inner-cell"
                            style="width:{{ $sc(74) }}px; height:{{ $sc(74) }}px; font-size:{{ $sc(8) }}px; letter-spacing:{{ $sc(0.5) }}px; line-height:1.5;">
                            RESMI<br>{{ $certificate->issued_at ? \Carbon\Carbon::parse($certificate->issued_at)->year : date('Y') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if ($signName || $signatureImageExists)
            <div class="cert-signature"
                style="bottom:{{ $sc(70) }}px; right:{{ $sc(55) }}px; width:{{ $sc(220) }}px;">
                @if ($signatureImageExists)
                    <img class="sign-image" src="{{ $signatureImagePath }}"
                        style="height:{{ $sc(90) }}px; max-width:{{ $sc(210) }}px; margin-bottom:{{ $sc(4) }}px;">
                @else
                    <div style="height:{{ $sc(90) }}px;"></div>
                @endif
                <div class="sign-line" style="margin-bottom:{{ $sc(7) }}px;"></div>
                @if ($signName)
                    <div class="sign-name" style="font-size:{{ $sc(13) }}px;">{{ $signName }}</div>
                @endif
                @if ($signRole)
                    <div class="sign-role"
                        style="font-size:{{ $sc(8.5) }}px; letter-spacing:{{ $sc(1) }}px; margin-top:{{ $sc(3) }}px;">
                        {{ $signRole }}
                    </div>
                @endif
            </div>
        @endif

        @if ($issuedDate)
            <div class="cert-date" style="bottom:{{ $sc(65) }}px; font-size:{{ $sc(12) }}px;">
                Diterbitkan pada {{ $issuedDate }}
            </div>
        @endif

        <div class="cert-number" style="bottom:{{ $sc(34) }}px; font-size:{{ $sc(10) }}px;">
            No. {{ $certificate->certificate_number }}
        </div>
    </div>

</body>

</html>
