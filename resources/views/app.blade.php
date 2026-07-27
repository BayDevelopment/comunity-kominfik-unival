<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @class(['dark' => ($appearance ?? 'system') == 'dark'])>

<head>
    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- SEO --}}
    <title>
        {{ config('app.name', 'KOMINFIK - Komunitas Mahasiswa Informatika Universitas Al-Khairiyah') }}
    </title>

    <meta name="description"
        content="KOMINFIK adalah platform komunitas mahasiswa Informatika Universitas Al-Khairiyah untuk informasi, kegiatan, layanan, dan kolaborasi mahasiswa.">

    <meta name="keywords"
        content="KOMINFIK, Komunitas Mahasiswa Informatika, Informatika, Universitas Al-Khairiyah, Teknik Informatika, kominfik unival">

    <meta name="author" content="KOMINFIK">

    <meta name="robots" content="index, follow">

    <link rel="canonical" href="{{ url()->current() }}">


    {{-- Open Graph --}}
    <meta property="og:title" content="KOMINFIK - Komunitas Mahasiswa Informatika">

    <meta property="og:description" content="Platform komunitas mahasiswa Informatika Universitas Al-Khairiyah.">

    <meta property="og:type" content="website">

    <meta property="og:url" content="{{ url()->current() }}">

    <meta property="og:image" content="{{ asset('icon-512.png') }}">

    <meta property="og:site_name" content="KOMINFIK">


    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">

    <meta name="twitter:title" content="KOMINFIK - Komunitas Mahasiswa Informatika">

    <meta name="twitter:description" content="Platform komunitas mahasiswa Informatika Universitas Al-Khairiyah.">

    <meta name="twitter:image" content="{{ asset('icon-512.png') }}">


    {{-- PWA --}}
    <link rel="icon" href="/favicon.ico" sizes="any">

    <link rel="icon" href="/favicon.svg" type="image/svg+xml">

    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    <link rel="manifest" href="/build/manifest.webmanifest">


    <meta name="theme-color" content="#f97316">

    <meta name="mobile-web-app-capable" content="yes">

    <meta name="apple-mobile-web-app-capable" content="yes">

    <meta name="apple-mobile-web-app-status-bar-style" content="default">

    <meta name="apple-mobile-web-app-title" content="KOMINFIK">


    {{-- Structured Data SEO --}}
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "KOMINFIK",
        "description": "Komunitas Mahasiswa Informatika Universitas Al-Khairiyah",
        "url": "{{ url('/') }}",
        "logo": "{{ asset('icon-512.png') }}"
    }
    </script>


    {{-- Theme --}}
    <script>
        (function() {
            const appearance = '{{ $appearance ?? 'system' }}';

            if (appearance === 'system') {
                const prefersDark =
                    window.matchMedia('(prefers-color-scheme: dark)').matches;

                if (prefersDark) {
                    document.documentElement.classList.add('dark');
                }
            }
        })();
    </script>


    {{-- Background prevent flicker --}}
    <style>
        html {
            background-color: oklch(1 0 0);
        }

        html.dark {
            background-color: oklch(0.145 0 0);
        }
    </style>


    @fonts


    @vite(['resources/css/app.css', 'resources/js/app.ts', "resources/js/pages/{$page['component']}.vue"])

</head>


<body class="font-sans antialiased">

    <x-inertia::app />

</body>

</html>
