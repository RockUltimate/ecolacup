{{-- resources/views/layouts/guest.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'EcolaCup') }}</title>
    <script>
        if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
            document.documentElement.classList.add('dark');
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Newsreader:ital,opsz,wght@0,6..72,200..800;1,6..72,200..800&family=Manrope:wght@200..800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-background text-on-surface antialiased dark:bg-[#1c1c19] dark:text-[#e5e2dd]">
    <div class="flex min-h-screen items-center justify-center px-4 py-10 sm:px-6 lg:px-8">
        <div class="w-full max-w-md">
            {{-- Logo --}}
            <div class="mb-8 text-center">
                <a href="{{ route('udalosti.index') }}"
                   class="font-headline text-3xl italic text-emerald-950 dark:text-emerald-100">
                    EcolaCup
                </a>
            </div>

            {{-- Glass card --}}
            <div class="glass-card px-8 py-8">
                {{ $slot }}
            </div>
        </div>
    </div>
</body>
</html>
