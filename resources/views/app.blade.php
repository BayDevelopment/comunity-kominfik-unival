<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    @class(['dark' => ($appearance ?? 'system') == 'dark'])>

<head>
    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- SEO --}}
    <meta name="description"
        content="KOMINFIK adalah platform resmi Komunitas Mahasiswa Informatika Universitas Al-Khairiyah untuk informasi kegiatan, layanan, kolaborasi, dan pengembangan mahasiswa.">

    <meta name="keywords"
        content="KOMINFIK, Komunitas Mahasiswa Informatika, kominfik unival, Informatika, Teknik Informatika, KOMINFIK UNIVAL">

    <meta name="author"
        content="KOMINFIK">

    <meta name="robots"
        content="index, follow">

    {{-- Open Graph --}}
    <meta property="og:type"
        content="website">

    <meta property="og:site_name"
        content="KOMINFIK">

    <meta property="og:title"
        content="Komunitas Mahasiswa Informatika - Universitas Al-Khairiyah">

    <meta property="og:description"
        content="Platform resmi Komunitas Mahasiswa Informatika Universitas Al-Khairiyah.">

    <meta property="og:image"
        content="{{ asset('icon-512.png') }}">

    {{-- Twitter --}}
    <meta name="twitter:card"
        content="summary_large_image">

    <meta name="twitter:title"
        content="Komunitas Mahasiswa Informatika - Universitas Al-Khairiyah">

    <meta name="twitter:description"
        content="Platform resmi Komunitas Mahasiswa Informatika Universitas Al-Khairiyah.">

    <meta name="twitter:image"
        content="{{ asset('icon-512.png') }}">

    {{-- Theme Detection --}}
    <script>
        (function() {
            const appearance = '{{ $appearance ?? 'system' }}';

            if (appearance === 'system') {
                if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                    document.documentElement.classList.add('dark');
                }
            }
        })();
    </script>

    {{-- Prevent white flash --}}
    <style>
        html {
            background-color: oklch(1 0 0);
        }

        html.dark {
            background-color: oklch(0.145 0 0);
        }
    </style>

    {{-- Icons --}}
    <link rel="icon"
        href="/favicon.ico"
        sizes="any">

    <link rel="icon"
        href="/favicon.svg"
        type="image/svg+xml">

    <link rel="apple-touch-icon"
        href="/apple-touch-icon.png">

    {{-- PWA --}}
    <link rel="manifest"
        href="/build/manifest.webmanifest">

    <meta name="theme-color"
        content="#f97316">

    <meta name="mobile-web-app-capable"
        content="yes">

    <meta name="apple-mobile-web-app-capable"
        content="yes">

    <meta name="apple-mobile-web-app-status-bar-style"
        content="default">

    <meta name="apple-mobile-web-app-title"
        content="KOMINFIK">

    @fonts

    @vite([
        'resources/css/app.css',
        'resources/js/app.ts',
        "resources/js/pages/{$page['component']}.vue"
    ])

    <x-inertia::head>
        <title>{{ config('app.name', 'Komunitas Mahasiswa Informatika - Universitas Al-Khairiyah') }}</title>
    </x-inertia::head>

</head>

<body class="font-sans antialiased">

    <x-inertia::app />

</body>

</html>
