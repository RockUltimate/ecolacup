<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

        <title>{{ config('app.name', 'ECOLAKONĚ') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="site-shell text-gray-900 antialiased">
        <div class="site-chroma" aria-hidden="true"></div>
        <div class="relative flex min-h-screen items-center justify-center px-4 py-10 sm:px-6 lg:px-8">
            <div class="grid w-full max-w-6xl gap-8 lg:grid-cols-[minmax(0,1.1fr)_minmax(360px,0.9fr)] lg:items-center">
                <section class="hidden lg:block">
                    <p class="section-eyebrow">ECOLAKONĚ</p>
                    <h1 class="mt-4 max-w-xl text-5xl leading-tight text-[#20392c]">Přihlášky na koňské závody bez chaotických tabulek a ručního přepisování.</h1>
                    <p class="mt-5 max-w-xl text-base leading-7 text-gray-600">Účet drží pohromadě osoby, koně i registrace. Přihlášení účastníci pak řeší další závody během pár kroků.</p>
                    <div class="mt-8 flex flex-wrap gap-3">
                        <a href="{{ route('udalosti.index') }}" class="button-primary">Zobrazit události</a>
                        <a href="{{ route('gdpr') }}" class="button-secondary">GDPR informace</a>
                    </div>
                </section>

                <section class="panel px-6 py-7 sm:px-8">
                    <div class="mb-6">
                        <a href="{{ route('udalosti.index') }}" class="flex items-center gap-3">
                            <span class="site-mark">EC</span>
                            <span>
                                <span class="block text-sm font-semibold tracking-[0.08em] text-[#7b5230]">ECOLAKONĚ</span>
                                <span class="block text-xs text-gray-500">Registrace na koňské závody</span>
                            </span>
                        </a>
                    </div>
                    {{ $slot }}
                </section>
            </div>
        </div>
    </body>
</html>
