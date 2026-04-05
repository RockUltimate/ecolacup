# Stitch → Blade Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Implement the Heritage Field Stitch design system across all EcolaCup Blade views, replacing current tokens and rebuilding the 5 key public/user pages, while all other pages inherit uniform styling via the shared component library.

**Architecture:** Token-first approach — update Tailwind config with the full Stitch colour scale, rebuild `app.css` component classes using those tokens, update all three shared layouts, then rebuild the 5 key Stitch pages and fix hardcoded colours in remaining views. Dark mode via `darkMode: 'class'` + OS-preference script.

**Tech Stack:** Laravel 11 + Blade, Tailwind CSS v3, Alpine.js, Vite, PHP 8.4, Docker (port 8086)

**Stitch export:** `/c/Users/Rock Ultimate/Documents/Web/ecolakone/stitch/` (5 pages with `code.html`, plus `paddock_reserve/DESIGN.md`)

**Design spec:** `docs/superpowers/specs/2026-04-05-stitch-blade-design.md`

---

## File Map

**Modified (infrastructure):**
- `tailwind.config.js` — add Stitch colour tokens, fonts, border-radius, darkMode
- `resources/css/app.css` — rebuild all component classes with new tokens

**Modified (layouts — affects every page):**
- `resources/views/components/site-layout.blade.php` — public layout: font, fixed nav, footer
- `resources/views/layouts/guest.blade.php` — auth layout: glass card, font
- `resources/views/layouts/app.blade.php` — app layout: font, sidebar styling, chroma removal
- `resources/views/layouts/navigation.blade.php` — app nav: token colours

**Rebuilt (Stitch pages):**
- `resources/views/udalosti/index.blade.php` — landing + race discovery
- `resources/views/udalosti/show.blade.php` — race detail
- `resources/views/prihlasky/index.blade.php` — user dashboard
- `resources/views/prihlasky/_form.blade.php` — registration form
- `resources/views/admin/dashboard.blade.php` — organiser dashboard

**Colour-fixed (inherit component library, remove hardcoded colours):**
- `resources/views/kone/index.blade.php`, `create.blade.php`, `edit.blade.php`
- `resources/views/osoby/index.blade.php`, `create.blade.php`, `edit.blade.php`
- `resources/views/clenstvi-cmt/index.blade.php`, `create.blade.php`, `edit.blade.php`
- `resources/views/prihlasky/show.blade.php`, `create.blade.php`, `edit.blade.php`
- `resources/views/profile/edit.blade.php`
- `resources/views/auth/login.blade.php`, `register.blade.php`, `forgot-password.blade.php`, `reset-password.blade.php`, `verify-email.blade.php`, `confirm-password.blade.php`
- `resources/views/admin/udalosti/index.blade.php`, `show.blade.php`, `create.blade.php`, `edit.blade.php`
- `resources/views/admin/reports/prihlasky.blade.php`, `startky.blade.php`, `ubytovani.blade.php`
- `resources/views/admin/users/index.blade.php`, `edit.blade.php`
- `resources/views/admin/clenstvi/index.blade.php`, `edit.blade.php`
- `resources/views/gdpr.blade.php`

**Created (tests):**
- `tests/Feature/DesignRenderTest.php`

---

## Task 1: Create `frontend` branch

**Files:** none (git only)

- [ ] **Step 1: Push current worktree work as `frontend` branch on origin**

```bash
cd "C:\Users\Rock Ultimate\Documents\Web\ecolakone\.claude\worktrees\sleepy-zhukovsky"
git push origin HEAD:frontend
```

Expected output: `* [new branch] HEAD -> frontend`

- [ ] **Step 2: Confirm branch exists on remote**

```bash
git ls-remote --heads origin frontend
```

Expected: a line with `refs/heads/frontend`

---

## Task 2: Tailwind Config — Stitch Colour Tokens

**Files:**
- Modify: `tailwind.config.js`

- [ ] **Step 1: Replace `tailwind.config.js` with Stitch token config**

```js
// tailwind.config.js
import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],
    theme: {
        extend: {
            colors: {
                'primary':                    '#173809',
                'on-primary':                 '#ffffff',
                'primary-container':          '#2d4f1e',
                'on-primary-container':       '#98c083',
                'primary-fixed':              '#c5efad',
                'primary-fixed-dim':          '#a9d293',
                'on-primary-fixed':           '#062100',
                'on-primary-fixed-variant':   '#2d4f1e',
                'secondary':                  '#77574d',
                'on-secondary':               '#ffffff',
                'secondary-container':        '#fed3c7',
                'on-secondary-container':     '#795950',
                'secondary-fixed':            '#ffdbd0',
                'secondary-fixed-dim':        '#e7bdb1',
                'on-secondary-fixed':         '#2c160e',
                'on-secondary-fixed-variant': '#5d4037',
                'tertiary':                   '#422c0a',
                'on-tertiary':                '#ffffff',
                'tertiary-container':         '#5b421e',
                'on-tertiary-container':      '#d2af82',
                'tertiary-fixed':             '#ffddb3',
                'tertiary-fixed-dim':         '#e5c192',
                'on-tertiary-fixed':          '#291800',
                'on-tertiary-fixed-variant':  '#5b421f',
                'surface':                    '#fcf9f4',
                'surface-dim':                '#dcdad5',
                'surface-bright':             '#fcf9f4',
                'surface-container-lowest':   '#ffffff',
                'surface-container-low':      '#f6f3ee',
                'surface-container':          '#f0ede8',
                'surface-container-high':     '#ebe8e3',
                'surface-container-highest':  '#e5e2dd',
                'surface-variant':            '#e5e2dd',
                'surface-tint':               '#446733',
                'on-surface':                 '#1c1c19',
                'on-surface-variant':         '#43493e',
                'outline':                    '#73796d',
                'outline-variant':            '#c3c8bb',
                'inverse-surface':            '#31302d',
                'inverse-on-surface':         '#f3f0eb',
                'inverse-primary':            '#a9d293',
                'error':                      '#ba1a1a',
                'on-error':                   '#ffffff',
                'error-container':            '#ffdad6',
                'on-error-container':         '#93000a',
                'background':                 '#fcf9f4',
                'on-background':              '#1c1c19',
            },
            borderRadius: {
                DEFAULT: '0.125rem',
                lg:      '0.25rem',
                xl:      '0.5rem',
                full:    '0.75rem',
            },
            fontFamily: {
                headline: ['Newsreader', 'serif'],
                body:     ['Manrope', 'sans-serif'],
                label:    ['Manrope', 'sans-serif'],
                serif:    ['Newsreader', 'serif'],
                sans:     ['Manrope', ...defaultTheme.fontFamily.sans],
            },
        },
    },
    plugins: [forms],
};
```

- [ ] **Step 2: Verify Tailwind compiles without errors**

```bash
cd "C:\Users\Rock Ultimate\Documents\Web\ecolakone\.claude\worktrees\sleepy-zhukovsky"
npm run build 2>&1
```

Expected: build completes, `public/build/assets/app-*.css` written. No errors.

- [ ] **Step 3: Commit**

```bash
git add tailwind.config.js
git commit -m "feat: add Stitch colour tokens and dark mode to Tailwind config

Co-Authored-By: Claude Sonnet 4.6 <noreply@anthropic.com>"
```

---

## Task 3: CSS Component Library

**Files:**
- Modify: `resources/css/app.css`

- [ ] **Step 1: Replace `resources/css/app.css` with Stitch component library**

```css
@tailwind base;
@tailwind components;
@tailwind utilities;

@layer base {
    body {
        @apply bg-background text-on-surface antialiased;
        font-family: theme('fontFamily.body');
    }

    h1, h2, h3, h4 {
        font-family: theme('fontFamily.headline');
        letter-spacing: 0.01em;
    }

    [x-cloak] { display: none !important; }
}

@layer components {

    /* ── Navigation ─────────────────────────────────────────── */
    .site-nav {
        @apply fixed top-0 z-50 w-full backdrop-blur-md;
        background: rgba(252, 249, 244, 0.82);
    }
    .dark .site-nav {
        background: rgba(28, 28, 25, 0.82);
    }

    .site-mark {
        @apply flex h-11 w-11 items-center justify-center rounded-full text-sm font-bold;
        background: linear-gradient(135deg, #173809 0%, #2d4f1e 100%);
        color: #ffffff;
        box-shadow: 0 8px 24px rgba(23, 56, 9, 0.25);
    }

    /* ── Cards / Panels ──────────────────────────────────────── */
    .panel {
        @apply rounded-[1.5rem];
        background-color: theme('colors.surface-container-lowest');
        border: 1px solid rgba(195, 200, 187, 0.2);
        box-shadow: 0 8px 40px rgba(28, 28, 25, 0.08);
    }
    .dark .panel {
        background-color: theme('colors.surface-container');
        border-color: rgba(67, 73, 62, 0.3);
        box-shadow: 0 8px 40px rgba(0, 0, 0, 0.25);
    }

    .glass-card {
        @apply rounded-xl;
        backdrop-filter: blur(16px);
        background: rgba(252, 249, 244, 0.85);
        border: 1px solid rgba(195, 200, 187, 0.2);
    }
    .dark .glass-card {
        background: rgba(28, 28, 25, 0.85);
        border-color: rgba(67, 73, 62, 0.3);
    }

    /* ── Buttons ─────────────────────────────────────────────── */
    .button-primary {
        @apply inline-flex items-center justify-center rounded-lg px-6 py-2.5 text-sm font-bold uppercase tracking-widest transition-all duration-300;
        background: linear-gradient(135deg, #173809 0%, #2d4f1e 100%);
        color: #ffffff;
        box-shadow: 0 6px 20px rgba(23, 56, 9, 0.2);
    }
    .button-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 26px rgba(23, 56, 9, 0.28);
    }

    .button-secondary {
        @apply inline-flex items-center justify-center rounded-lg px-6 py-2.5 text-sm font-bold uppercase tracking-widest transition-all duration-300;
        background-color: theme('colors.surface-container-highest');
        color: theme('colors.on-surface');
    }
    .button-secondary:hover {
        background-color: theme('colors.surface-container-high');
    }
    .dark .button-secondary {
        background-color: theme('colors.surface-container-high');
        color: theme('colors.on-surface');
    }
    .dark .button-secondary:hover {
        background-color: theme('colors.surface-container');
    }

    /* ── Form fields ─────────────────────────────────────────── */
    .field-shell {
        @apply mt-1 block w-full bg-transparent px-0 py-3 text-sm;
        border: none;
        border-bottom: 2px solid theme('colors.outline-variant');
        border-radius: 0;
        outline: none;
        box-shadow: none;
        color: theme('colors.on-surface');
        transition: border-color 0.2s;
    }
    .field-shell:hover  { border-bottom-color: theme('colors.outline'); }
    .field-shell:focus  {
        border-bottom-color: theme('colors.primary');
        outline: none;
        box-shadow: none;
    }
    .dark .field-shell {
        border-bottom-color: #43493e;
        color: theme('colors.on-surface');
        background: transparent;
    }
    .dark .field-shell:focus { border-bottom-color: #a9d293; }

    /* ── Chips / Pills ───────────────────────────────────────── */
    .brand-pill {
        @apply inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold;
        background-color: theme('colors.secondary-container');
        color: theme('colors.on-secondary-container');
    }
    .dark .brand-pill {
        background-color: #5d4037;
        color: #ffdbd0;
    }

    /* ── Typography helpers ──────────────────────────────────── */
    .brand-link {
        @apply underline underline-offset-4 transition hover:opacity-70;
        color: theme('colors.secondary');
    }
    .dark .brand-link { color: theme('colors.secondary-fixed-dim'); }

    .section-eyebrow {
        @apply text-xs font-bold uppercase tracking-[0.3em];
        color: theme('colors.primary');
    }
    .dark .section-eyebrow { color: theme('colors.inverse-primary'); }

    /* ── Notifications / status blocks ──────────────────────── */
    .status-note {
        @apply rounded-xl px-4 py-3 text-sm leading-6;
        background-color: theme('colors.secondary-container');
        color: theme('colors.on-secondary-container');
        border: 1px solid rgba(195, 200, 187, 0.15);
    }
    .dark .status-note {
        background-color: #5d4037;
        color: #ffdbd0;
    }

    /* ── Layout helpers ──────────────────────────────────────── */
    .editorial-grid {
        @apply grid gap-6 lg:grid-cols-[minmax(0,1.3fr)_minmax(320px,0.7fr)];
    }

    /* ── Animations ──────────────────────────────────────────── */
    .reveal-up       { animation: reveal-up 0.7s cubic-bezier(0.22, 1, 0.36, 1) both; }
    .reveal-up-delay { animation: reveal-up 0.9s cubic-bezier(0.22, 1, 0.36, 1) both; }
}

@keyframes reveal-up {
    from { opacity: 0; transform: translateY(22px); }
    to   { opacity: 1; transform: translateY(0); }
}
```

- [ ] **Step 2: Rebuild and verify**

```bash
npm run build 2>&1
```

Expected: build succeeds.

- [ ] **Step 3: Commit**

```bash
git add resources/css/app.css
git commit -m "feat: rebuild CSS component library with Stitch design tokens

Co-Authored-By: Claude Sonnet 4.6 <noreply@anthropic.com>"
```

---

## Task 4: Public Layout — `site-layout.blade.php`

**Files:**
- Modify: `resources/views/components/site-layout.blade.php`

- [ ] **Step 1: Replace `site-layout.blade.php`**

```blade
{{-- resources/views/components/site-layout.blade.php --}}
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
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-background text-on-surface antialiased dark:bg-[#1c1c19] dark:text-[#e5e2dd]">

    {{-- Fixed glassmorphic nav --}}
    <nav class="site-nav">
        <div class="mx-auto flex max-w-screen-xl items-center justify-between gap-6 px-6 py-4 lg:px-8">
            {{-- Logo --}}
            <a href="{{ route('udalosti.index') }}" class="font-headline text-2xl italic text-emerald-950 dark:text-emerald-100">
                EcolaCup
            </a>

            {{-- Centre links --}}
            <div class="hidden items-center gap-8 md:flex">
                <a href="{{ route('udalosti.index') }}"
                   class="text-sm font-semibold tracking-wide transition-colors
                          {{ request()->routeIs('udalosti.*') ? 'border-b-2 border-primary pb-1 text-primary dark:border-inverse-primary dark:text-inverse-primary' : 'text-on-surface-variant hover:text-on-surface dark:text-[#c3c8bb] dark:hover:text-[#e5e2dd]' }}">
                    Události
                </a>
                <a href="{{ route('gdpr') }}"
                   class="text-sm font-semibold tracking-wide text-on-surface-variant transition-colors hover:text-on-surface dark:text-[#c3c8bb] dark:hover:text-[#e5e2dd]">
                    GDPR
                </a>
                @auth
                    <a href="{{ route('prihlasky.index') }}"
                       class="text-sm font-semibold tracking-wide transition-colors
                              {{ request()->routeIs('prihlasky.*') ? 'border-b-2 border-primary pb-1 text-primary dark:border-inverse-primary dark:text-inverse-primary' : 'text-on-surface-variant hover:text-on-surface dark:text-[#c3c8bb] dark:hover:text-[#e5e2dd]' }}">
                        Moje přihlášky
                    </a>
                    @if(auth()->user()->is_admin)
                        <a href="{{ route('admin.dashboard') }}"
                           class="text-sm font-semibold tracking-wide text-on-surface-variant transition-colors hover:text-on-surface dark:text-[#c3c8bb] dark:hover:text-[#e5e2dd]">
                            Admin
                        </a>
                    @endif
                @endauth
            </div>

            {{-- Right CTA --}}
            <div class="flex items-center gap-4">
                @auth
                    <a href="{{ route('dashboard') }}" class="button-primary hidden sm:inline-flex">
                        Otevřít aplikaci
                    </a>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-semibold text-on-surface-variant hover:opacity-80 dark:text-[#c3c8bb]">
                        Přihlásit se
                    </a>
                    <a href="{{ route('register') }}" class="button-primary">
                        Vytvořit účet
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    {{-- Page content pushed below fixed nav --}}
    <div class="pt-[72px]">
        <x-flash-message />
        {{ $slot }}
    </div>

    {{-- Footer --}}
    <footer class="mt-16 rounded-t-3xl bg-stone-100 dark:bg-stone-950">
        <div class="mx-auto grid max-w-screen-xl gap-12 px-6 py-16 md:grid-cols-3 lg:px-8">
            {{-- Brand --}}
            <div class="space-y-4">
                <p class="font-headline text-xl italic text-emerald-900 dark:text-emerald-100">EcolaCup</p>
                <p class="text-sm leading-relaxed text-on-surface-variant dark:text-[#c3c8bb]">
                    Přehled událostí, registrace jezdců a koní, exporty pro pořadatele Czech Mountain Trail závodů.
                </p>
                <div class="flex gap-4">
                    <span class="material-symbols-outlined cursor-pointer text-on-surface-variant transition-colors hover:text-primary dark:text-[#8d9387] dark:hover:text-[#a9d293]">public</span>
                    <span class="material-symbols-outlined cursor-pointer text-on-surface-variant transition-colors hover:text-primary dark:text-[#8d9387] dark:hover:text-[#a9d293]">mail</span>
                </div>
            </div>

            {{-- Links --}}
            <div class="grid grid-cols-2 gap-8">
                <div class="space-y-3">
                    <p class="text-sm font-bold text-on-surface dark:text-[#e5e2dd]">Rychlé odkazy</p>
                    <a href="{{ route('udalosti.index') }}" class="block text-xs uppercase tracking-widest text-on-surface-variant transition-colors hover:text-primary dark:text-[#8d9387] dark:hover:text-[#a9d293]">Kalendář událostí</a>
                    <a href="{{ route('gdpr') }}" class="block text-xs uppercase tracking-widest text-on-surface-variant transition-colors hover:text-primary dark:text-[#8d9387] dark:hover:text-[#a9d293]">GDPR</a>
                </div>
                <div class="space-y-3">
                    <p class="text-sm font-bold text-on-surface dark:text-[#e5e2dd]">Účet</p>
                    @auth
                        <a href="{{ route('prihlasky.index') }}" class="block text-xs uppercase tracking-widest text-on-surface-variant transition-colors hover:text-primary dark:text-[#8d9387] dark:hover:text-[#a9d293]">Moje přihlášky</a>
                        <a href="{{ route('ucet.edit') }}" class="block text-xs uppercase tracking-widest text-on-surface-variant transition-colors hover:text-primary dark:text-[#8d9387] dark:hover:text-[#a9d293]">Můj účet</a>
                    @else
                        <a href="{{ route('login') }}" class="block text-xs uppercase tracking-widest text-on-surface-variant transition-colors hover:text-primary dark:text-[#8d9387] dark:hover:text-[#a9d293]">Přihlášení</a>
                        <a href="{{ route('register') }}" class="block text-xs uppercase tracking-widest text-on-surface-variant transition-colors hover:text-primary dark:text-[#8d9387] dark:hover:text-[#a9d293]">Registrace</a>
                    @endauth
                </div>
            </div>

            {{-- Newsletter (UI only — no backend) --}}
            <div class="space-y-4">
                <p class="section-eyebrow">Novinky</p>
                <p class="text-xs text-on-surface-variant dark:text-[#c3c8bb]">Dostávejte oznámení o nových závodech a termínech.</p>
                <div class="flex">
                    <input type="email" placeholder="váš@email.cz"
                           class="field-shell w-full rounded-none border-b-2 border-outline-variant bg-transparent text-xs focus:border-primary dark:border-[#43493e] dark:focus:border-[#a9d293]">
                    <button class="button-primary rounded-l-none px-4 py-2 text-xs" disabled>
                        <span class="material-symbols-outlined text-sm">send</span>
                    </button>
                </div>
                <p class="text-[10px] uppercase tracking-widest text-on-surface-variant dark:text-[#8d9387]">
                    © {{ date('Y') }} EcolaCup
                </p>
            </div>
        </div>
    </footer>
</body>
</html>
```

- [ ] **Step 2: Verify public homepage renders**

```bash
php artisan view:cache --quiet && php artisan view:clear --quiet
```

Expected: no errors. Then open `http://localhost:8086` in a browser — nav should be fixed, glassmorphic, showing "EcolaCup" in italic Newsreader.

- [ ] **Step 3: Commit**

```bash
git add resources/views/components/site-layout.blade.php
git commit -m "feat: update public layout with Stitch nav, footer, and dark mode script

Co-Authored-By: Claude Sonnet 4.6 <noreply@anthropic.com>"
```

---

## Task 5: Auth Layout + App Layout

**Files:**
- Modify: `resources/views/layouts/guest.blade.php`
- Modify: `resources/views/layouts/app.blade.php`
- Modify: `resources/views/layouts/navigation.blade.php`

- [ ] **Step 1: Replace `layouts/guest.blade.php`**

```blade
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
```

- [ ] **Step 2: Replace `layouts/app.blade.php`**

```blade
{{-- resources/views/layouts/app.blade.php --}}
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
    @include('layouts.navigation')

    @isset($header)
        <header class="border-b border-outline-variant/20 bg-surface-container-low/60 backdrop-blur-sm dark:border-[#43493e]/30 dark:bg-[#252522]/60">
            <div class="mx-auto max-w-7xl px-4 py-7 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
    @endisset

    <main class="relative mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <x-flash-message />

        @php
            $upcomingEvents = \App\Models\Udalost::query()
                ->where('aktivni', true)
                ->whereDate('datum_zacatek', '>=', now()->startOfDay())
                ->orderBy('datum_zacatek')
                ->limit(3)
                ->get();
            $myOpenRegistrations = auth()->check()
                ? auth()->user()->prihlasky()->with(['udalost', 'kun'])->where('smazana', false)->latest()->limit(4)->get()
                : collect();
        @endphp

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-[minmax(0,1fr)_300px]">
            <div>{{ $slot }}</div>
            <aside class="space-y-4">
                <section class="panel p-5">
                    <p class="section-eyebrow">Kalendář</p>
                    <h3 class="mt-2 font-headline text-lg text-primary dark:text-inverse-primary">Nadcházející události</h3>
                    <ul class="mt-3 space-y-2 text-sm">
                        @forelse($upcomingEvents as $event)
                            <li>
                                <a href="{{ route('udalosti.show', $event) }}" class="brand-link">{{ $event->nazev }}</a>
                                <p class="text-on-surface-variant dark:text-[#c3c8bb]">{{ $event->datum_zacatek?->format('d.m.Y') }}</p>
                            </li>
                        @empty
                            <li class="text-on-surface-variant dark:text-[#c3c8bb]">Momentálně nejsou vypsané žádné akce.</li>
                        @endforelse
                    </ul>
                </section>
                <section class="panel p-5">
                    <p class="section-eyebrow">Moje agenda</p>
                    <h3 class="mt-2 font-headline text-lg text-secondary dark:text-secondary-fixed-dim">Moje přihlášky</h3>
                    <ul class="mt-3 space-y-2 text-sm">
                        @forelse($myOpenRegistrations as $registration)
                            <li>
                                <a href="{{ route('prihlasky.show', $registration) }}" class="brand-link">{{ $registration->udalost?->nazev }}</a>
                                <p class="text-on-surface-variant dark:text-[#c3c8bb]">{{ $registration->kun?->jmeno }}</p>
                            </li>
                        @empty
                            <li class="text-on-surface-variant dark:text-[#c3c8bb]">Zatím nemáte aktivní přihlášky.</li>
                        @endforelse
                    </ul>
                </section>
            </aside>
        </div>
    </main>
</body>
</html>
```

- [ ] **Step 3: Replace `layouts/navigation.blade.php`**

```blade
{{-- resources/views/layouts/navigation.blade.php --}}
<nav class="site-nav">
    <div class="mx-auto flex max-w-7xl items-center justify-between gap-6 px-4 py-4 sm:px-6 lg:px-8">
        <a href="{{ route('udalosti.index') }}" class="flex items-center gap-3">
            <span class="site-mark">EC</span>
            <span class="flex flex-col">
                <span class="text-sm font-bold uppercase tracking-[0.28em] text-secondary dark:text-secondary-fixed-dim">EcolaCup</span>
                <span class="text-xs text-on-surface-variant dark:text-[#8d9387]">Czech Mountain Trail</span>
            </span>
        </a>

        <nav class="hidden items-center gap-6 text-sm md:flex">
            <a href="{{ route('udalosti.index') }}" class="brand-link">Události</a>
            <a href="{{ route('prihlasky.index') }}" class="brand-link">Přihlášky</a>
            @if(auth()->user()?->is_admin)
                <a href="{{ route('admin.dashboard') }}" class="brand-link">Admin</a>
            @endif
        </nav>

        <div class="flex items-center gap-3">
            <a href="{{ route('ucet.edit') }}"
               class="text-sm font-semibold text-on-surface-variant transition hover:text-on-surface dark:text-[#c3c8bb] dark:hover:text-[#e5e2dd]">
                {{ auth()->user()?->name }}
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="button-secondary px-4 py-2 text-xs">
                    Odhlásit
                </button>
            </form>
        </div>
    </div>
</nav>
```

- [ ] **Step 4: Verify authenticated pages compile**

```bash
php artisan view:clear --quiet
```

Expected: no errors.

- [ ] **Step 5: Commit**

```bash
git add resources/views/layouts/guest.blade.php resources/views/layouts/app.blade.php resources/views/layouts/navigation.blade.php
git commit -m "feat: update auth and app layouts with Stitch tokens and dark mode

Co-Authored-By: Claude Sonnet 4.6 <noreply@anthropic.com>"
```

---

## Task 6: Test Scaffolding

**Files:**
- Create: `tests/Feature/DesignRenderTest.php`

- [ ] **Step 1: Create `tests/Feature/DesignRenderTest.php`**

```php
<?php

namespace Tests\Feature;

use App\Models\Udalost;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DesignRenderTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_renders_with_site_layout(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('EcolaCup');
        $response->assertSee('site-nav', false);
        $response->assertSee('button-primary', false);
    }

    public function test_event_detail_renders(): void
    {
        $udalost = Udalost::factory()->create(['aktivni' => true]);
        $response = $this->get(route('udalosti.show', $udalost));
        $response->assertStatus(200);
        $response->assertSee($udalost->nazev);
    }

    public function test_login_page_renders_with_glass_card(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('glass-card', false);
        $response->assertSee('EcolaCup');
    }

    public function test_register_page_renders(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
        $response->assertSee('glass-card', false);
    }

    public function test_user_dashboard_requires_auth(): void
    {
        $response = $this->get(route('prihlasky.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_user_dashboard_renders_for_authenticated_user(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('prihlasky.index'));
        $response->assertStatus(200);
        $response->assertSee('Moje přihlášky');
    }

    public function test_horses_page_renders_for_authenticated_user(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('kone.index'));
        $response->assertStatus(200);
        $response->assertSee('panel', false);
    }

    public function test_persons_page_renders_for_authenticated_user(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('osoby.index'));
        $response->assertStatus(200);
        $response->assertSee('panel', false);
    }

    public function test_admin_dashboard_requires_admin(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $response = $this->actingAs($user)->get(route('admin.dashboard'));
        $response->assertStatus(403);
    }

    public function test_admin_dashboard_renders_for_admin(): void
    {
        $user = User::factory()->create(['is_admin' => true]);
        $response = $this->actingAs($user)->get(route('admin.dashboard'));
        $response->assertStatus(200);
    }
}
```

- [ ] **Step 2: Run the tests — expect some failures (views not yet rebuilt)**

```bash
php artisan test tests/Feature/DesignRenderTest.php --stop-on-error 2>&1
```

Expected: `test_homepage_renders_with_site_layout` and `test_login_page_renders_with_glass_card` should pass. Others may fail if factories are missing — that is fine for now; we fix them as we build.

- [ ] **Step 3: Commit**

```bash
git add tests/Feature/DesignRenderTest.php
git commit -m "test: add design render tests for all key pages

Co-Authored-By: Claude Sonnet 4.6 <noreply@anthropic.com>"
```

---

## Task 7: `udalosti/index.blade.php` — Landing + Race Discovery

**Files:**
- Modify: `resources/views/udalosti/index.blade.php`

Reference: `stitch/landing_page/code.html` + `stitch/race_discovery/code.html`

Data available: `$upcoming` (Collection of `Udalost`), `$archive` (Collection), `$openEvents` (int). No image assets — use CSS gradient placeholders.

- [ ] **Step 1: Replace `resources/views/udalosti/index.blade.php`**

```blade
{{-- resources/views/udalosti/index.blade.php --}}
<x-site-layout>

{{-- ── Hero ─────────────────────────────────────────────────────── --}}
<section class="relative min-h-[600px] overflow-hidden">
    <div class="mx-auto grid max-w-screen-xl items-center gap-8 px-6 py-20 lg:grid-cols-12 lg:px-8">

        {{-- Left: headline --}}
        <div class="relative z-10 lg:col-span-6">
            <p class="section-eyebrow mb-4">Kalendár akcí</p>
            <h1 class="font-headline text-5xl leading-tight text-primary dark:text-inverse-primary sm:text-6xl lg:text-7xl">
                Moderní přihlášky<br><span class="italic">na CMT závody.</span>
            </h1>
            <p class="mt-6 max-w-lg text-lg leading-relaxed text-on-surface-variant dark:text-[#c3c8bb]">
                Veřejný kalendář, přehled uzávěrek, disciplín a kapacit. Přihlášení jezdci navazují rovnou na správu osob, koní a přihlášek.
            </p>
            <div class="mt-8 flex flex-wrap gap-4">
                @auth
                    <a href="{{ route('dashboard') }}" class="button-primary px-10 py-4">Pokračovat do aplikace</a>
                @else
                    <a href="{{ route('register') }}" class="button-primary px-10 py-4">Začít registraci</a>
                    <a href="{{ route('login') }}" class="button-secondary px-10 py-4">Mám účet</a>
                @endauth
            </div>
        </div>

        {{-- Right: stats / next event card --}}
        <div class="lg:col-span-6">
            @if($upcoming->isNotEmpty())
                @php $featured = $upcoming->first(); @endphp
                <div class="panel reveal-up-delay overflow-hidden">
                    {{-- Gradient hero image placeholder --}}
                    <div class="h-48 w-full"
                         style="background: linear-gradient(135deg, #173809 0%, #2d4f1e 40%, #446733 100%);">
                        <div class="flex h-full items-end p-6">
                            <span class="brand-pill">Nejbližší akce</span>
                        </div>
                    </div>
                    <div class="p-8">
                        <h2 class="font-headline text-3xl text-on-surface dark:text-[#e5e2dd]">{{ $featured->nazev }}</h2>
                        <p class="mt-2 text-on-surface-variant dark:text-[#c3c8bb]">{{ $featured->misto }} • {{ $featured->datum_zacatek?->format('d.m.Y') }}</p>
                        <a href="{{ route('udalosti.show', $featured) }}" class="button-primary mt-6 inline-flex">
                            Zobrazit detail
                        </a>
                    </div>
                </div>
            @else
                <div class="panel reveal-up-delay p-12 text-center">
                    <p class="font-headline text-2xl italic text-on-surface-variant dark:text-[#c3c8bb]">Brzy budou vypsány nové akce.</p>
                </div>
            @endif
        </div>
    </div>
</section>

{{-- ── Stats bar ────────────────────────────────────────────────── --}}
<section class="bg-surface-container-low py-10 dark:bg-[#252522]">
    <div class="mx-auto grid max-w-screen-xl grid-cols-3 gap-6 px-6 text-center lg:px-8">
        <div>
            <p class="font-headline text-4xl text-primary dark:text-inverse-primary">{{ $upcoming->count() }}</p>
            <p class="mt-1 text-sm text-on-surface-variant dark:text-[#c3c8bb]">nadcházejících událostí</p>
        </div>
        <div>
            <p class="font-headline text-4xl text-primary dark:text-inverse-primary">{{ $openEvents }}</p>
            <p class="mt-1 text-sm text-on-surface-variant dark:text-[#c3c8bb]">otevřených registrací</p>
        </div>
        <div>
            <p class="font-headline text-4xl text-primary dark:text-inverse-primary">{{ $archive->count() }}</p>
            <p class="mt-1 text-sm text-on-surface-variant dark:text-[#c3c8bb]">akcí v archivu</p>
        </div>
    </div>
</section>

{{-- ── Upcoming races bento grid ────────────────────────────────── --}}
@if($upcoming->count() > 0)
<section class="py-20">
    <div class="mx-auto max-w-screen-xl px-6 lg:px-8">
        <div class="mb-10 flex items-end justify-between">
            <div>
                <p class="section-eyebrow">Sezóna</p>
                <h2 class="mt-2 font-headline text-4xl text-on-surface dark:text-[#e5e2dd]">Nadcházející akce</h2>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
            @foreach($upcoming as $i => $udalost)
                @php
                    $closed = ($udalost->uzavierka_prihlasek && $udalost->uzavierka_prihlasek->lt(now()->startOfDay()))
                           || ($udalost->kapacita !== null && $udalost->pocet_prihlasek >= $udalost->kapacita);
                    $isFirst = $i === 0;
                @endphp

                @if($isFirst)
                {{-- Featured large card --}}
                <div class="panel reveal-up overflow-hidden md:col-span-2 md:row-span-2 hover:shadow-xl transition-shadow">
                    <div class="relative h-64"
                         style="background: linear-gradient(135deg, #173809 0%, #2d4f1e 50%, #446733 100%);">
                        <div class="absolute left-4 top-4">
                            <span class="brand-pill">{{ $closed ? 'Uzavřeno' : 'Registrace otevřena' }}</span>
                        </div>
                    </div>
                    <div class="p-8">
                        <h3 class="font-headline text-3xl text-on-surface dark:text-[#e5e2dd]">{{ $udalost->nazev }}</h3>
                        <p class="mt-2 text-on-surface-variant dark:text-[#c3c8bb]">{{ $udalost->misto }} • {{ $udalost->datum_zacatek?->format('d.m.Y') }}</p>
                        @if($udalost->uzavierka_prihlasek)
                            <p class="mt-1 text-sm text-on-surface-variant dark:text-[#c3c8bb]">Uzávěrka: {{ $udalost->uzavierka_prihlasek->format('d.m.Y') }}</p>
                        @endif
                        @if($udalost->kapacita)
                            <p class="mt-1 text-sm text-on-surface-variant dark:text-[#c3c8bb]">{{ $udalost->pocet_prihlasek }} / {{ $udalost->kapacita }} přihlášek</p>
                        @endif
                        <a href="{{ route('udalosti.show', $udalost) }}" class="button-primary mt-6 inline-flex">Detail akce</a>
                    </div>
                </div>
                @else
                {{-- Smaller cards --}}
                <div class="panel reveal-up flex items-center gap-5 p-6 transition-colors hover:bg-surface-container-low dark:hover:bg-[#2a2a27]">
                    <div class="h-16 w-16 flex-shrink-0 rounded-xl"
                         style="background: linear-gradient(135deg, #173809 0%, #446733 100%);"></div>
                    <div class="min-w-0">
                        <h3 class="font-headline text-lg truncate text-on-surface dark:text-[#e5e2dd]">{{ $udalost->nazev }}</h3>
                        <p class="text-sm text-on-surface-variant dark:text-[#c3c8bb]">{{ $udalost->datum_zacatek?->format('d.m.Y') }}</p>
                        <a href="{{ route('udalosti.show', $udalost) }}"
                           class="mt-2 inline-block text-xs font-bold uppercase tracking-widest text-primary underline underline-offset-4 dark:text-inverse-primary">
                            Rezervovat místo
                        </a>
                    </div>
                </div>
                @endif
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ── Archive ───────────────────────────────────────────────────── --}}
@if($archive->isNotEmpty())
<section class="bg-surface-container-low py-20 dark:bg-[#252522]">
    <div class="mx-auto max-w-screen-xl px-6 lg:px-8">
        <p class="section-eyebrow mb-6">Archiv</p>
        <div class="space-y-0">
            @foreach($archive as $udalost)
                <div class="flex items-center justify-between py-5
                            {{ !$loop->last ? 'border-b border-outline-variant/20 dark:border-[#43493e]/30' : '' }}">
                    <div>
                        <p class="font-semibold text-on-surface dark:text-[#e5e2dd]">{{ $udalost->nazev }}</p>
                        <p class="text-sm text-on-surface-variant dark:text-[#c3c8bb]">{{ $udalost->misto }} • {{ $udalost->datum_zacatek?->format('d.m.Y') }}</p>
                    </div>
                    <a href="{{ route('udalosti.show', $udalost) }}"
                       class="ml-4 flex-shrink-0 text-sm font-bold uppercase tracking-widest text-primary transition hover:opacity-70 dark:text-inverse-primary">
                        Zobrazit
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ── CTA Banner ───────────────────────────────────────────────── --}}
<section class="relative overflow-hidden py-32">
    <div class="absolute inset-0"
         style="background: linear-gradient(135deg, #173809 0%, #2d4f1e 50%, #446733 100%);"></div>
    <div class="absolute inset-0 backdrop-blur-sm" style="background: rgba(23,56,9,0.6);"></div>
    <div class="relative z-10 mx-auto max-w-3xl px-8 text-center">
        <h2 class="font-headline text-5xl text-white sm:text-6xl">Připraveni na start?</h2>
        <p class="mt-6 text-xl text-white/80">
            Vytvořte účet a přihlaste se na nadcházející závody během pár minut.
        </p>
        <div class="mt-10 flex flex-col items-center gap-4 sm:flex-row sm:justify-center">
            @auth
                <a href="{{ route('dashboard') }}" class="bg-white px-10 py-4 rounded-lg text-sm font-bold uppercase tracking-widest text-primary hover:bg-stone-100 transition shadow-2xl">
                    Otevřít aplikaci
                </a>
            @else
                <a href="{{ route('register') }}" class="bg-white px-10 py-4 rounded-lg text-sm font-bold uppercase tracking-widest text-primary hover:bg-stone-100 transition shadow-2xl">
                    Vytvořit profil jezdce
                </a>
                <a href="{{ route('udalosti.index') }}" class="border-2 border-white/30 px-10 py-4 rounded-lg text-sm font-bold uppercase tracking-widest text-white hover:bg-white/10 transition">
                    Prohlédnout akce
                </a>
            @endauth
        </div>
    </div>
</section>

</x-site-layout>
```

- [ ] **Step 2: Verify homepage**

Open `http://localhost:8086` — should show hero section, stats bar, bento grid, archive, CTA banner.

- [ ] **Step 3: Run render test**

```bash
php artisan test tests/Feature/DesignRenderTest.php::test_homepage_renders_with_site_layout
```

Expected: PASS

- [ ] **Step 4: Commit**

```bash
git add resources/views/udalosti/index.blade.php
git commit -m "feat: rebuild events index as Heritage Field landing + discovery page

Co-Authored-By: Claude Sonnet 4.6 <noreply@anthropic.com>"
```

---

## Task 8: `udalosti/show.blade.php` — Race Detail

**Files:**
- Modify: `resources/views/udalosti/show.blade.php`

Reference: `stitch/race_detail_registration/code.html`
Data: `$udalost` with `->moznosti` (disciplines) and `->ustajeniMoznosti` (stabling options).

- [ ] **Step 1: Replace `resources/views/udalosti/show.blade.php`**

```blade
{{-- resources/views/udalosti/show.blade.php --}}
<x-site-layout>

@php
    $closed = ($udalost->uzavierka_prihlasek && $udalost->uzavierka_prihlasek->lt(now()->startOfDay()))
           || ($udalost->kapacita !== null && $udalost->pocet_prihlasek >= $udalost->kapacita);
@endphp

{{-- ── Hero ─────────────────────────────────────────────────────── --}}
<section class="relative min-h-[600px] overflow-hidden">
    <div class="mx-auto flex max-w-screen-xl items-center gap-8 px-6 py-20 lg:px-8">

        {{-- Left: event info --}}
        <div class="relative z-10 w-full lg:w-3/5">
            <span class="brand-pill mb-6 inline-block">CMT Závod</span>
            <h1 class="font-headline text-6xl leading-none text-primary dark:text-inverse-primary lg:text-8xl">
                {{ $udalost->nazev }}
            </h1>
            <p class="mt-6 max-w-lg text-xl text-on-surface-variant dark:text-[#c3c8bb]">
                {{ $udalost->misto }}
                @if($udalost->datum_zacatek)
                    • {{ $udalost->datum_zacatek->format('d.m.Y') }}
                    @if($udalost->datum_konec && $udalost->datum_konec->ne($udalost->datum_zacatek))
                        – {{ $udalost->datum_konec->format('d.m.Y') }}
                    @endif
                @endif
            </p>
            <div class="mt-8 flex flex-wrap gap-4">
                @if(!$closed)
                    @auth
                        <a href="{{ route('prihlasky.create', $udalost) }}" class="button-primary px-10 py-4">
                            Přihlásit se
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="button-primary px-10 py-4">
                            Přihlásit se
                        </a>
                    @endauth
                @else
                    <span class="button-secondary cursor-not-allowed px-10 py-4 opacity-60">Registrace uzavřena</span>
                @endif
                <a href="{{ route('udalosti.index') }}" class="button-secondary px-10 py-4">
                    Všechny akce
                </a>
            </div>
        </div>

        {{-- Right: gradient image placeholder --}}
        <div class="absolute right-0 top-0 hidden h-full w-2/5 lg:block">
            <div class="h-full w-full overflow-hidden" style="border-radius: 2rem 0.5rem 0.5rem 5rem;">
                <div class="h-full w-full"
                     style="background: linear-gradient(135deg, #173809 0%, #2d4f1e 50%, #446733 100%); opacity: 0.85;"></div>
            </div>
        </div>
    </div>
</section>

{{-- ── Stats bento grid ─────────────────────────────────────────── --}}
<section class="px-6 py-16 lg:px-8">
    <div class="mx-auto grid max-w-screen-xl grid-cols-1 gap-6 md:grid-cols-4">

        {{-- Kapacita --}}
        <div class="rounded-xl bg-surface-container-low p-8 dark:bg-[#252522]">
            <h3 class="font-headline text-2xl text-primary dark:text-inverse-primary">Kapacita</h3>
            @if($udalost->kapacita)
                <div class="mt-4 flex items-baseline gap-2">
                    <span class="font-headline text-5xl text-primary dark:text-inverse-primary">{{ $udalost->pocet_prihlasek }}</span>
                    <span class="text-sm uppercase tracking-widest text-on-surface-variant dark:text-[#c3c8bb]">/ {{ $udalost->kapacita }}</span>
                </div>
                <p class="mt-2 text-sm text-on-surface-variant dark:text-[#c3c8bb]">obsazených míst</p>
            @else
                <p class="mt-4 font-headline text-3xl text-primary dark:text-inverse-primary">{{ $udalost->pocet_prihlasek }}</p>
                <p class="mt-1 text-sm text-on-surface-variant dark:text-[#c3c8bb]">přihlášek (bez limitu)</p>
            @endif
        </div>

        {{-- Uzávěrka --}}
        <div class="rounded-xl bg-surface-container-highest p-8 dark:bg-[#3b3b38]">
            <h3 class="font-headline text-xl text-primary dark:text-inverse-primary">Uzávěrka</h3>
            <p class="mt-4 font-headline text-3xl text-on-surface dark:text-[#e5e2dd]">
                {{ $udalost->uzavierka_prihlasek?->format('d.m.Y') ?? '—' }}
            </p>
            <p class="mt-1 text-sm text-on-surface-variant dark:text-[#c3c8bb]">přihlášek</p>
        </div>

        {{-- Status --}}
        <div class="flex flex-col items-center justify-center rounded-xl p-8 text-center
                    {{ $closed ? 'bg-inverse-surface text-inverse-on-surface dark:bg-[#31302d]' : 'bg-primary text-on-primary' }}">
            <p class="text-xs uppercase tracking-widest opacity-80">Stav</p>
            <p class="mt-2 font-headline text-2xl italic">{{ $closed ? 'Uzavřeno' : 'Otevřeno' }}</p>
        </div>

        {{-- Schedule --}}
        <div class="rounded-xl bg-surface-container-low p-8 dark:bg-[#252522]">
            <h3 class="mb-4 text-xs font-bold uppercase tracking-widest text-primary dark:text-inverse-primary">Termíny</h3>
            <ul class="space-y-3 text-sm">
                @if($udalost->datum_zacatek)
                    <li class="flex justify-between border-b border-outline-variant/20 pb-2 dark:border-[#43493e]/30">
                        <span class="font-semibold text-on-surface dark:text-[#e5e2dd]">Zahájení</span>
                        <span class="text-on-surface-variant dark:text-[#c3c8bb]">{{ $udalost->datum_zacatek->format('d.m.Y') }}</span>
                    </li>
                @endif
                @if($udalost->datum_konec)
                    <li class="flex justify-between border-b border-outline-variant/20 pb-2 dark:border-[#43493e]/30">
                        <span class="font-semibold text-on-surface dark:text-[#e5e2dd]">Ukončení</span>
                        <span class="text-on-surface-variant dark:text-[#c3c8bb]">{{ $udalost->datum_konec->format('d.m.Y') }}</span>
                    </li>
                @endif
                @if($udalost->uzavierka_prihlasek)
                    <li class="flex justify-between">
                        <span class="font-semibold text-on-surface dark:text-[#e5e2dd]">Uzávěrka</span>
                        <span class="text-on-surface-variant dark:text-[#c3c8bb]">{{ $udalost->uzavierka_prihlasek->format('d.m.Y') }}</span>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</section>

{{-- ── Disciplines ──────────────────────────────────────────────── --}}
@if($udalost->moznosti->isNotEmpty())
<section class="bg-surface-container-low py-20 dark:bg-[#252522]">
    <div class="mx-auto grid max-w-screen-xl gap-16 px-6 lg:grid-cols-2 lg:px-8">
        <div>
            <h2 class="font-headline text-4xl text-primary dark:text-inverse-primary">Disciplíny</h2>
            <p class="mt-4 text-on-surface-variant dark:text-[#c3c8bb]">
                Přehled kategorií a startovních poplatků pro tuto akci.
            </p>
        </div>
        <div class="space-y-0">
            @foreach($udalost->moznosti as $moznost)
                <div class="flex items-center justify-between py-4
                            {{ !$loop->last ? 'border-b border-outline-variant/20 dark:border-[#43493e]/30' : '' }}">
                    <div>
                        <p class="font-semibold text-on-surface dark:text-[#e5e2dd]">{{ $moznost->nazev }}</p>
                        @if($moznost->popis)
                            <p class="text-sm text-on-surface-variant dark:text-[#c3c8bb]">{{ $moznost->popis }}</p>
                        @endif
                    </div>
                    <span class="ml-4 font-headline text-xl text-primary dark:text-inverse-primary">
                        {{ number_format($moznost->cena, 0, ',', ' ') }} Kč
                    </span>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ── Stabling ─────────────────────────────────────────────────── --}}
@if($udalost->ustajeniMoznosti->isNotEmpty())
<section class="py-20">
    <div class="mx-auto max-w-screen-xl px-6 lg:px-8">
        <h2 class="mb-10 font-headline text-4xl text-on-surface dark:text-[#e5e2dd]">Ustájení</h2>
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($udalost->ustajeniMoznosti as $ustajeni)
                <div class="rounded-xl bg-surface-container-highest p-6 dark:bg-[#3b3b38]">
                    <h3 class="font-headline text-xl text-on-surface dark:text-[#e5e2dd]">{{ $ustajeni->nazev }}</h3>
                    @if($ustajeni->popis)
                        <p class="mt-2 text-sm text-on-surface-variant dark:text-[#c3c8bb]">{{ $ustajeni->popis }}</p>
                    @endif
                    <p class="mt-4 font-headline text-2xl text-primary dark:text-inverse-primary">
                        {{ number_format($ustajeni->cena, 0, ',', ' ') }} Kč
                    </p>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ── Registration CTA ─────────────────────────────────────────── --}}
@if(!$closed)
<section class="bg-surface-container-low py-20 dark:bg-[#252522]">
    <div class="mx-auto max-w-screen-xl px-6 text-center lg:px-8">
        <h2 class="font-headline text-4xl text-on-surface dark:text-[#e5e2dd]">Přihlásit se na tuto akci</h2>
        <p class="mt-4 text-on-surface-variant dark:text-[#c3c8bb]">Uzávěrka přihlášek: {{ $udalost->uzavierka_prihlasek?->format('d.m.Y') ?? 'neurčena' }}</p>
        <div class="mt-8">
            @auth
                <a href="{{ route('prihlasky.create', $udalost) }}" class="button-primary px-12 py-5 text-base">
                    Přihlásit se
                </a>
            @else
                <a href="{{ route('register') }}" class="button-primary px-12 py-5 text-base">
                    Vytvořit účet a přihlásit se
                </a>
            @endauth
        </div>
    </div>
</section>
@endif

</x-site-layout>
```

- [ ] **Step 2: Run render test**

```bash
php artisan test tests/Feature/DesignRenderTest.php::test_event_detail_renders
```

Expected: PASS

- [ ] **Step 3: Commit**

```bash
git add resources/views/udalosti/show.blade.php
git commit -m "feat: rebuild event detail page with Stitch race detail layout

Co-Authored-By: Claude Sonnet 4.6 <noreply@anthropic.com>"
```

---

## Task 9: `prihlasky/index.blade.php` — User Dashboard

**Files:**
- Modify: `resources/views/prihlasky/index.blade.php`

Reference: `stitch/user_dashboard/code.html`

- [ ] **Step 1: Read current `prihlasky/index.blade.php` to understand data variables**

```bash
cat resources/views/prihlasky/index.blade.php
```

Note the variable names passed from `PrihlaskaController::index()` — typically `$prihlasky`.

- [ ] **Step 2: Replace `resources/views/prihlasky/index.blade.php`**

```blade
{{-- resources/views/prihlasky/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="section-eyebrow">Moje záznamy</p>
                <h1 class="mt-2 font-headline text-4xl italic text-primary dark:text-inverse-primary">Moje přihlášky</h1>
            </div>
            <a href="{{ route('udalosti.index') }}" class="button-primary">
                + Nová přihláška
            </a>
        </div>
    </x-slot>

    {{-- ── Metrics bento ──────────────────────────────────────── --}}
    <div class="grid grid-cols-2 gap-6 lg:grid-cols-4">
        @php
            $active = $prihlasky->where('smazana', false)
                ->filter(fn($p) => $p->udalost?->datum_konec?->gte(now()) ?? false)->count();
            $upcoming = $prihlasky->where('smazana', false)
                ->filter(fn($p) => $p->udalost?->datum_zacatek?->gt(now()) ?? false)->count();
            $nextEntry = $prihlasky->where('smazana', false)
                ->filter(fn($p) => $p->udalost?->datum_zacatek?->gt(now()) ?? false)
                ->sortBy(fn($p) => $p->udalost?->datum_zacatek)
                ->first();
        @endphp

        <div class="panel relative overflow-hidden p-6">
            <p class="text-xs font-bold uppercase tracking-widest text-on-surface-variant dark:text-[#c3c8bb]">Aktivní přihlášky</p>
            <p class="mt-3 font-headline text-4xl text-primary dark:text-inverse-primary">{{ $active }}</p>
        </div>

        <div class="panel relative overflow-hidden p-6">
            <p class="text-xs font-bold uppercase tracking-widest text-on-surface-variant dark:text-[#c3c8bb]">Nadcházející</p>
            <p class="mt-3 font-headline text-4xl text-primary dark:text-inverse-primary">{{ $upcoming }}</p>
        </div>

        <div class="panel col-span-2 p-6">
            <p class="text-xs font-bold uppercase tracking-widest text-on-surface-variant dark:text-[#c3c8bb]">Příští akce</p>
            @if($nextEntry)
                <p class="mt-3 font-headline text-2xl text-on-surface dark:text-[#e5e2dd]">{{ $nextEntry->udalost?->nazev }}</p>
                <p class="mt-1 text-sm text-on-surface-variant dark:text-[#c3c8bb]">
                    {{ $nextEntry->udalost?->datum_zacatek?->format('d.m.Y') }}
                    • {{ $nextEntry->kun?->jmeno }}
                </p>
            @else
                <p class="mt-3 text-on-surface-variant dark:text-[#c3c8bb]">Žádná nadcházející akce.</p>
            @endif
        </div>
    </div>

    {{-- ── Registration list ──────────────────────────────────── --}}
    <div class="mt-8 space-y-4">
        <h2 class="font-headline text-2xl italic text-on-surface dark:text-[#e5e2dd]">Všechny přihlášky</h2>

        @forelse($prihlasky->where('smazana', false) as $prihlaska)
            @php
                $eventPast = $prihlaska->udalost?->datum_konec?->lt(now()) ?? false;
            @endphp
            <div class="panel flex flex-col gap-5 p-6 transition-shadow hover:shadow-md sm:flex-row sm:items-center">
                {{-- Gradient thumbnail --}}
                <div class="h-20 w-24 flex-shrink-0 overflow-hidden rounded-xl"
                     style="background: linear-gradient(135deg, #173809 0%, #2d4f1e 100%);"></div>

                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2 mb-1">
                        <span class="brand-pill">{{ $eventPast ? 'Absolvováno' : 'Aktivní' }}</span>
                        @if($prihlaska->moznosti->isNotEmpty())
                            @foreach($prihlaska->moznosti->take(2) as $moznost)
                                <span class="rounded-full bg-surface-container-high px-2 py-0.5 text-xs font-semibold text-on-surface dark:bg-[#313130] dark:text-[#e5e2dd]">
                                    {{ $moznost->nazev }}
                                </span>
                            @endforeach
                        @endif
                    </div>
                    <p class="font-semibold text-on-surface dark:text-[#e5e2dd]">{{ $prihlaska->udalost?->nazev }}</p>
                    <p class="text-sm text-on-surface-variant dark:text-[#c3c8bb]">
                        {{ $prihlaska->udalost?->datum_zacatek?->format('d.m.Y') }}
                        @if($prihlaska->kun) • {{ $prihlaska->kun->jmeno }} @endif
                        @if($prihlaska->osoba) • {{ $prihlaska->osoba->jmeno_prijmeni }} @endif
                    </p>
                </div>

                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('prihlasky.show', $prihlaska) }}" class="button-secondary px-4 py-2 text-xs">Detail</a>
                    @if(!$eventPast)
                        <a href="{{ route('prihlasky.edit', $prihlaska) }}" class="button-secondary px-4 py-2 text-xs">Upravit</a>
                    @endif
                    <a href="{{ route('prihlasky.pdf', $prihlaska) }}" class="button-secondary px-4 py-2 text-xs">PDF</a>
                </div>
            </div>
        @empty
            <div class="panel p-10 text-center">
                <p class="font-headline text-xl text-on-surface-variant dark:text-[#c3c8bb]">Zatím žádné přihlášky.</p>
                <a href="{{ route('udalosti.index') }}" class="button-primary mt-6 inline-flex">Prohlédnout akce</a>
            </div>
        @endforelse
    </div>

    {{-- ── Quick links ─────────────────────────────────────────── --}}
    <div class="mt-8 grid grid-cols-3 gap-4">
        <a href="{{ route('kone.index') }}"
           class="panel flex flex-col items-center gap-2 p-5 text-center transition-colors hover:bg-surface-container-low dark:hover:bg-[#2a2a27]">
            <span class="font-headline text-2xl text-primary dark:text-inverse-primary">🐎</span>
            <span class="text-sm font-semibold text-on-surface dark:text-[#e5e2dd]">Moje koně</span>
        </a>
        <a href="{{ route('osoby.index') }}"
           class="panel flex flex-col items-center gap-2 p-5 text-center transition-colors hover:bg-surface-container-low dark:hover:bg-[#2a2a27]">
            <span class="font-headline text-2xl text-primary dark:text-inverse-primary">👤</span>
            <span class="text-sm font-semibold text-on-surface dark:text-[#e5e2dd]">Moje osoby</span>
        </a>
        <a href="{{ route('clenstvi-cmt.index') }}"
           class="panel flex flex-col items-center gap-2 p-5 text-center transition-colors hover:bg-surface-container-low dark:hover:bg-[#2a2a27]">
            <span class="font-headline text-2xl text-primary dark:text-inverse-primary">🏆</span>
            <span class="text-sm font-semibold text-on-surface dark:text-[#e5e2dd]">CMT členství</span>
        </a>
    </div>

</x-app-layout>
```

- [ ] **Step 3: Run render test**

```bash
php artisan test tests/Feature/DesignRenderTest.php::test_user_dashboard_renders_for_authenticated_user
```

Expected: PASS

- [ ] **Step 4: Commit**

```bash
git add resources/views/prihlasky/index.blade.php
git commit -m "feat: rebuild user dashboard with Stitch metrics bento and card list

Co-Authored-By: Claude Sonnet 4.6 <noreply@anthropic.com>"
```

---

## Task 10: `prihlasky/_form.blade.php` — Registration Form

**Files:**
- Modify: `resources/views/prihlasky/_form.blade.php`

Reference: `stitch/paddock_reserve/DESIGN.md` — tonal sections, bottom-border inputs, no boxed borders, glass-card sidebar.

- [ ] **Step 1: Read the existing `prihlasky/_form.blade.php` in full to understand all form fields and Alpine.js interactions**

```bash
cat resources/views/prihlasky/_form.blade.php
```

Record all `x-data`, `x-show`, `@change` handlers, AJAX calls, and form field names before proceeding.

- [ ] **Step 2: Wrap every form section with tonal background instead of bordered boxes**

The form has distinct sections (person selection, horse selection, disciplines, stabling, fees). Wrap each section in a tonal container rather than a bordered card:

```blade
{{-- Replace any bordered section wrappers like: --}}
{{-- <div class="panel p-6"> --}}

{{-- With tonal background sections: --}}
<div class="rounded-2xl bg-surface-container-low px-6 py-8 dark:bg-[#252522]">
    <h2 class="font-headline text-2xl text-on-surface dark:text-[#e5e2dd]">Osoba</h2>
    {{-- section content --}}
</div>

{{-- Nested items inside a section get slightly higher surface: --}}
<div class="rounded-xl bg-surface-container px-4 py-4 dark:bg-[#2a2a27]">
    {{-- item content --}}
</div>
```

- [ ] **Step 3: Replace `<x-text-input>` component usages with `.field-shell` inputs**

Find all `<x-text-input` tags and `<select` tags in `_form.blade.php` and replace with `.field-shell`:

```blade
{{-- Before --}}
<x-text-input id="poznamka" name="poznamka" type="text" :value="old('poznamka', $prihlaska->poznamka ?? '')" />

{{-- After --}}
<input id="poznamka" name="poznamka" type="text" value="{{ old('poznamka', $prihlaska->poznamka ?? '') }}"
       class="field-shell" />

{{-- Select before --}}
<select name="osoba_id" class="field-shell">

{{-- Select stays as-is — field-shell already applies to select via CSS --}}
```

- [ ] **Step 4: Update status/info banners to use `.status-note`**

```blade
{{-- Replace inline-styled or hardcoded info boxes with: --}}
<div class="status-note mt-4">
    {{ $infoText }}
</div>
```

- [ ] **Step 5: Add glass-card registration summary on desktop**

Locate the fee summary section and wrap it:

```blade
<aside class="glass-card sticky top-24 p-6 hidden lg:block">
    <p class="section-eyebrow mb-4">Souhrn přihlášky</p>
    {{-- existing fee summary content --}}
</aside>
```

- [ ] **Step 6: Verify form compiles and renders**

```bash
php artisan view:clear --quiet
```

Open `http://localhost:8086/udalosti/{id}/prihlasit` (replace `{id}` with any active event ID from the database) — form sections should use tonal backgrounds, inputs should have only bottom borders.

- [ ] **Step 7: Commit**

```bash
git add resources/views/prihlasky/_form.blade.php
git commit -m "feat: restyle registration form with tonal sections and bottom-border inputs

Co-Authored-By: Claude Sonnet 4.6 <noreply@anthropic.com>"
```

---

## Task 11: `admin/dashboard.blade.php` — Organiser Dashboard

**Files:**
- Modify: `resources/views/admin/dashboard.blade.php`

Reference: `stitch/event_management/code.html`

- [ ] **Step 1: Read current `admin/dashboard.blade.php`**

```bash
cat resources/views/admin/dashboard.blade.php
```

Note existing data: event counts, registration totals.

- [ ] **Step 2: Replace `resources/views/admin/dashboard.blade.php`**

```blade
{{-- resources/views/admin/dashboard.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="section-eyebrow">Administrace</p>
                <h1 class="mt-2 font-headline text-4xl italic text-primary dark:text-inverse-primary">
                    Přehled administrace
                </h1>
                <p class="mt-2 text-on-surface-variant dark:text-[#c3c8bb]">Správa událostí, přihlášek a uživatelů.</p>
            </div>
            <a href="{{ route('admin.udalosti.create') }}" class="button-primary flex items-center gap-2">
                + Vytvořit událost
            </a>
        </div>
    </x-slot>

    {{-- ── Metrics bento ──────────────────────────────────────── --}}
    <div class="grid grid-cols-2 gap-6 lg:grid-cols-4">
        <div class="panel relative overflow-hidden p-8">
            <p class="text-xs font-bold uppercase tracking-widest text-on-surface-variant dark:text-[#c3c8bb]">Aktivní události</p>
            <p class="mt-3 font-headline text-4xl text-primary dark:text-inverse-primary">{{ $aktivniCount ?? 0 }}</p>
            <div class="absolute -bottom-4 -right-4 text-8xl opacity-[0.07] select-none">🏁</div>
        </div>

        <div class="panel relative overflow-hidden p-8">
            <p class="text-xs font-bold uppercase tracking-widest text-on-surface-variant dark:text-[#c3c8bb]">Přihlášky celkem</p>
            <p class="mt-3 font-headline text-4xl text-primary dark:text-inverse-primary">{{ $prihlaskyCount ?? 0 }}</p>
            <div class="absolute -bottom-4 -right-4 text-8xl opacity-[0.07] select-none">📋</div>
        </div>

        <div class="panel col-span-2 p-8" style="background: linear-gradient(135deg, #2d4f1e 0%, #173809 100%);">
            <p class="text-xs font-bold uppercase tracking-widest text-white/70">Události v systému</p>
            <p class="mt-3 font-headline text-4xl text-white">{{ $celkemUdalosti ?? 0 }}</p>
            <div class="mt-4 flex items-center gap-4">
                <div class="h-1 flex-1 overflow-hidden rounded-full bg-white/20">
                    @php $pct = $celkemUdalosti > 0 ? min(100, ($aktivniCount / $celkemUdalosti) * 100) : 0; @endphp
                    <div class="h-full rounded-full bg-white/80" style="width: {{ $pct }}%;"></div>
                </div>
                <span class="text-xs font-bold text-white/80">{{ round($pct) }}% aktivních</span>
            </div>
        </div>
    </div>

    {{-- ── Upcoming events list ────────────────────────────────── --}}
    <div class="mt-10 space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="font-headline text-2xl italic text-on-surface dark:text-[#e5e2dd]">Nadcházející události</h2>
            <a href="{{ route('admin.udalosti.index') }}" class="text-sm font-bold uppercase tracking-widest text-primary underline underline-offset-4 dark:text-inverse-primary">
                Zobrazit vše
            </a>
        </div>

        @forelse($nadchazejici ?? [] as $udalost)
            <div class="panel flex flex-col items-stretch gap-6 p-6 transition-shadow hover:shadow-md lg:flex-row lg:items-center">
                {{-- Thumbnail --}}
                <div class="h-24 w-full flex-shrink-0 overflow-hidden rounded-xl lg:w-40"
                     style="background: linear-gradient(135deg, #173809 0%, #446733 100%);"></div>

                <div class="flex-1 min-w-0 space-y-2">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="brand-pill">{{ $udalost->aktivni ? 'Aktivní' : 'Archiv' }}</span>
                        <span class="text-sm text-on-surface-variant dark:text-[#c3c8bb]">
                            {{ $udalost->datum_zacatek?->format('d.m.Y') }}
                        </span>
                    </div>
                    <p class="font-headline text-xl text-on-surface dark:text-[#e5e2dd]">{{ $udalost->nazev }}</p>
                    <p class="text-sm text-on-surface-variant dark:text-[#c3c8bb]">{{ $udalost->misto }}</p>
                </div>

                <div class="grid grid-cols-2 gap-6 border-y border-outline-variant/20 py-4 lg:border-x lg:border-y-0 lg:px-8 lg:py-0 dark:border-[#43493e]/30">
                    <div>
                        <p class="text-xs uppercase tracking-widest text-on-surface-variant dark:text-[#c3c8bb]">Přihlášky</p>
                        <p class="mt-1 font-headline text-xl text-on-surface dark:text-[#e5e2dd]">
                            {{ $udalost->pocet_prihlasek }}{{ $udalost->kapacita ? ' / ' . $udalost->kapacita : '' }}
                        </p>
                    </div>
                </div>

                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('admin.udalosti.edit', $udalost) }}" class="button-secondary px-4 py-2 text-xs">Nastavení</a>
                    <a href="{{ route('admin.reports.prihlasky', $udalost) }}" class="button-primary px-4 py-2 text-xs">Přihlášky</a>
                </div>
            </div>
        @empty
            <div class="panel p-10 text-center">
                <p class="text-on-surface-variant dark:text-[#c3c8bb]">Zatím nejsou vytvořené žádné události.</p>
                <a href="{{ route('admin.udalosti.create') }}" class="button-primary mt-4 inline-flex">Vytvořit první událost</a>
            </div>
        @endforelse
    </div>

    {{-- ── Admin navigation shortcuts ──────────────────────────── --}}
    <div class="mt-10 grid grid-cols-2 gap-4 sm:grid-cols-4">
        <a href="{{ route('admin.udalosti.index') }}"
           class="panel p-5 text-center transition-colors hover:bg-surface-container-low dark:hover:bg-[#2a2a27]">
            <p class="font-bold text-on-surface dark:text-[#e5e2dd]">Události</p>
            <p class="mt-1 text-xs text-on-surface-variant dark:text-[#c3c8bb]">Správa akcí</p>
        </a>
        <a href="{{ route('admin.users.index') }}"
           class="panel p-5 text-center transition-colors hover:bg-surface-container-low dark:hover:bg-[#2a2a27]">
            <p class="font-bold text-on-surface dark:text-[#e5e2dd]">Uživatelé</p>
            <p class="mt-1 text-xs text-on-surface-variant dark:text-[#c3c8bb]">Správa účtů</p>
        </a>
        <a href="{{ route('admin.clenstvi.index') }}"
           class="panel p-5 text-center transition-colors hover:bg-surface-container-low dark:hover:bg-[#2a2a27]">
            <p class="font-bold text-on-surface dark:text-[#e5e2dd]">Členství CMT</p>
            <p class="mt-1 text-xs text-on-surface-variant dark:text-[#c3c8bb]">Správa členství</p>
        </a>
        <a href="{{ route('udalosti.index') }}"
           class="panel p-5 text-center transition-colors hover:bg-surface-container-low dark:hover:bg-[#2a2a27]">
            <p class="font-bold text-on-surface dark:text-[#e5e2dd]">Veřejný web</p>
            <p class="mt-1 text-xs text-on-surface-variant dark:text-[#c3c8bb]">Zobrazit jako návštěvník</p>
        </a>
    </div>

</x-app-layout>
```

- [ ] **Step 3: Check what variables the admin dashboard controller passes**

```bash
cat app/Http/Controllers/Admin/DashboardController.php
```

If the controller does not pass `$aktivniCount`, `$prihlaskyCount`, `$celkemUdalosti`, `$nadchazejici` — update the controller to pass them:

```php
// In DashboardController
use App\Models\Udalost;
use App\Models\Prihlaska;

public function __invoke()
{
    return view('admin.dashboard', [
        'aktivniCount'    => Udalost::where('aktivni', true)->count(),
        'celkemUdalosti'  => Udalost::count(),
        'prihlaskyCount'  => Prihlaska::where('smazana', false)->count(),
        'nadchazejici'    => Udalost::where('aktivni', true)
                                ->whereDate('datum_konec', '>=', now())
                                ->orderBy('datum_zacatek')
                                ->limit(5)
                                ->get(),
    ]);
}
```

- [ ] **Step 4: Run admin render test**

```bash
php artisan test tests/Feature/DesignRenderTest.php::test_admin_dashboard_renders_for_admin
```

Expected: PASS

- [ ] **Step 5: Commit**

```bash
git add resources/views/admin/dashboard.blade.php app/Http/Controllers/Admin/DashboardController.php
git commit -m "feat: rebuild admin dashboard with Stitch event management layout

Co-Authored-By: Claude Sonnet 4.6 <noreply@anthropic.com>"
```

---

## Task 12: CRUD Pages — Horses, Persons, Memberships

**Files:**
- Modify: `resources/views/kone/index.blade.php`, `create.blade.php`, `edit.blade.php`
- Modify: `resources/views/osoby/index.blade.php`, `create.blade.php`, `edit.blade.php`
- Modify: `resources/views/clenstvi-cmt/index.blade.php`, `create.blade.php`, `edit.blade.php`

These pages already use `.panel`, `.brand-pill`, `.button-primary`, `.button-secondary`. The token update in Task 2 handles most of the visual change. Only hardcoded colours need replacing.

- [ ] **Step 1: Fix hardcoded colours in `kone/index.blade.php`**

Replace every instance:

| Find | Replace |
|---|---|
| `text-indigo-600 hover:text-indigo-800 underline` | `brand-link` |
| `text-red-600 hover:text-red-800 underline` | `text-sm text-error underline underline-offset-4 dark:text-[#ffb4ab]` |
| `bg-emerald-100 text-emerald-700` | `bg-primary-fixed text-on-primary-fixed` |
| `bg-amber-100 text-amber-700` | `bg-tertiary-fixed text-on-tertiary-fixed` |
| `bg-red-100 text-red-700` | `bg-error-container text-on-error-container` |
| `bg-green-100 text-green-700` | `bg-primary-fixed text-on-primary-fixed` |
| `text-gray-900` | `text-on-surface dark:text-[#e5e2dd]` |
| `text-gray-600` | `text-on-surface-variant dark:text-[#c3c8bb]` |
| `text-indigo-600` (headings) | `text-primary dark:text-inverse-primary` |

Apply the same replacements to `osoby/index.blade.php`.

- [ ] **Step 2: Fix hardcoded colours in `kone/create.blade.php` and `kone/edit.blade.php`**

Replace `<x-text-input` components with `.field-shell` inputs where they appear outside the existing component context, and update any hardcoded colour classes using the same table above.

Replace `<x-input-label` with:
```blade
<label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant dark:text-[#c3c8bb]">
    {{ $labelText }}
</label>
```

Apply the same to `osoby/create.blade.php`, `osoby/edit.blade.php`.

- [ ] **Step 3: Fix `clenstvi-cmt/index.blade.php`**

Renewal status chips: replace hardcoded colour classes with `.brand-pill`:
```blade
{{-- Before --}}
<span class="inline-flex ... bg-green-100 text-green-700">Aktivní</span>

{{-- After --}}
<span class="brand-pill">Aktivní</span>
```

For expired/warning states use Tailwind tokens:
```blade
<span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold bg-error-container text-on-error-container">Expirováno</span>
```

- [ ] **Step 4: Verify pages compile and render**

```bash
php artisan test tests/Feature/DesignRenderTest.php::test_horses_page_renders_for_authenticated_user
php artisan test tests/Feature/DesignRenderTest.php::test_persons_page_renders_for_authenticated_user
```

Expected: both PASS

- [ ] **Step 5: Commit**

```bash
git add resources/views/kone/ resources/views/osoby/ resources/views/clenstvi-cmt/
git commit -m "feat: replace hardcoded colours in horse, person, and membership views

Co-Authored-By: Claude Sonnet 4.6 <noreply@anthropic.com>"
```

---

## Task 13: Auth Pages

**Files:**
- Modify: `resources/views/auth/login.blade.php`
- Modify: `resources/views/auth/register.blade.php`
- Modify: `resources/views/auth/forgot-password.blade.php`
- Modify: `resources/views/auth/reset-password.blade.php`
- Modify: `resources/views/auth/verify-email.blade.php`
- Modify: `resources/views/auth/confirm-password.blade.php`

The `guest.blade.php` layout (Task 5) already provides the glass card. These files only need their internal colour and typography classes updated.

- [ ] **Step 1: Update `auth/login.blade.php` — replace hardcoded colours**

Apply these replacements throughout:

| Find | Replace |
|---|---|
| `text-[#20392c]` | `text-on-surface dark:text-[#e5e2dd]` |
| `text-[#7b5230]` | `text-secondary dark:text-secondary-fixed-dim` |
| `text-gray-600` | `text-on-surface-variant dark:text-[#c3c8bb]` |
| `text-gray-700` | `text-on-surface dark:text-[#e5e2dd]` |
| `border-[#eadfcc]` | `border-outline-variant/30 dark:border-[#43493e]/30` |
| `bg-white/60` | `bg-surface-container-lowest/60 dark:bg-[#2a2a27]/60` |
| `rounded border-[#ccb28f] text-[#3d6b4f] focus:ring-[#3d6b4f]` | `rounded border-outline-variant text-primary focus:ring-primary` |
| `<x-primary-button>` | `<button type="submit" class="button-primary">` with closing `</button>` |

- [ ] **Step 2: Apply same replacements to `register.blade.php`**

Same table as Step 1.

- [ ] **Step 3: Apply same replacements to `forgot-password.blade.php`, `reset-password.blade.php`, `verify-email.blade.php`, `confirm-password.blade.php`**

Same table as Step 1 for each file.

- [ ] **Step 4: Run render tests**

```bash
php artisan test tests/Feature/DesignRenderTest.php::test_login_page_renders_with_glass_card
php artisan test tests/Feature/DesignRenderTest.php::test_register_page_renders
```

Expected: both PASS

- [ ] **Step 5: Commit**

```bash
git add resources/views/auth/
git commit -m "feat: update auth pages to use Stitch design tokens

Co-Authored-By: Claude Sonnet 4.6 <noreply@anthropic.com>"
```

---

## Task 14: Registration Show/Create/Edit + Profile

**Files:**
- Modify: `resources/views/prihlasky/show.blade.php`
- Modify: `resources/views/prihlasky/create.blade.php`
- Modify: `resources/views/prihlasky/edit.blade.php`
- Modify: `resources/views/profile/edit.blade.php`

- [ ] **Step 1: Update `prihlasky/show.blade.php`**

Apply the same colour replacement table from Task 13. Additionally:
- Replace any `<table` based discipline/fee lists with tonal alternating rows (no borders):
```blade
{{-- Replace table-based lists with: --}}
<div class="space-y-0">
    @foreach($items as $item)
        <div class="flex justify-between py-4 {{ !$loop->last ? 'border-b border-outline-variant/20 dark:border-[#43493e]/30' : '' }}">
            <span class="text-on-surface dark:text-[#e5e2dd]">{{ $item->name }}</span>
            <span class="font-semibold text-on-surface dark:text-[#e5e2dd]">{{ $item->value }}</span>
        </div>
    @endforeach
</div>
```

- [ ] **Step 2: Update `prihlasky/create.blade.php` and `prihlasky/edit.blade.php`**

These wrap `_form.blade.php`. Apply colour replacements to any outer page chrome (headers, breadcrumbs). The form itself was handled in Task 10.

- [ ] **Step 3: Update `profile/edit.blade.php`**

Apply the same colour replacements from Task 13. Profile form fields should use `.field-shell`. Profile sections use `.panel`.

- [ ] **Step 4: Verify pages compile**

```bash
php artisan view:clear --quiet
```

Expected: no errors.

- [ ] **Step 5: Commit**

```bash
git add resources/views/prihlasky/show.blade.php resources/views/prihlasky/create.blade.php resources/views/prihlasky/edit.blade.php resources/views/profile/
git commit -m "feat: update registration detail, wrappers, and profile views with Stitch tokens

Co-Authored-By: Claude Sonnet 4.6 <noreply@anthropic.com>"
```

---

## Task 15: Admin CRUD + Reports

**Files:**
- Modify: `resources/views/admin/udalosti/index.blade.php`, `show.blade.php`, `create.blade.php`, `edit.blade.php`
- Modify: `resources/views/admin/reports/prihlasky.blade.php`, `startky.blade.php`, `ubytovani.blade.php`
- Modify: `resources/views/admin/users/index.blade.php`, `edit.blade.php`
- Modify: `resources/views/admin/clenstvi/index.blade.php`, `edit.blade.php`

- [ ] **Step 1: Fix hardcoded colours in `admin/udalosti/index.blade.php`**

```blade
{{-- Replace --}}
text-[#20392c]   →  text-on-surface dark:text-[#e5e2dd]
text-gray-600    →  text-on-surface-variant dark:text-[#c3c8bb]
```

The index already uses `.panel`, `.brand-pill`, `.button-primary`, `.button-secondary` — those are correct.

- [ ] **Step 2: Fix filter toggle buttons in `admin/reports/prihlasky.blade.php`**

Replace hardcoded toggle button styles with token-based classes:

```blade
{{-- Before --}}
'border-[#20392c] bg-[#20392c] text-white' => ! $isDeletedView,
'border-[#ddd0bc] bg-white/70 text-gray-600' => $isDeletedView,

{{-- After --}}
'border-primary bg-primary text-on-primary dark:border-inverse-primary dark:bg-primary-container dark:text-on-primary-container' => ! $isDeletedView,
'border-outline-variant/40 bg-surface-container-lowest/70 text-on-surface-variant dark:border-[#43493e]/40 dark:bg-[#2a2a27]/70 dark:text-[#c3c8bb]' => $isDeletedView,
```

Apply same fix to `startky.blade.php` and `ubytovani.blade.php`.

- [ ] **Step 3: Fix `admin/users/index.blade.php` and `edit.blade.php`**

Apply the standard colour replacement table from Task 13.

- [ ] **Step 4: Fix `admin/clenstvi/index.blade.php` and `edit.blade.php`**

Apply the standard colour replacement table from Task 13.

- [ ] **Step 5: Verify admin pages compile**

```bash
php artisan view:clear --quiet
php artisan test tests/Feature/DesignRenderTest.php::test_admin_dashboard_renders_for_admin
```

Expected: PASS

- [ ] **Step 6: Commit**

```bash
git add resources/views/admin/
git commit -m "feat: update admin CRUD and report views with Stitch tokens

Co-Authored-By: Claude Sonnet 4.6 <noreply@anthropic.com>"
```

---

## Task 16: GDPR + Final Cleanup

**Files:**
- Modify: `resources/views/gdpr.blade.php`

- [ ] **Step 1: Update `gdpr.blade.php`**

Wrap prose content in a `.panel` inside `<x-site-layout>`:

```blade
<x-site-layout>
    <div class="mx-auto max-w-screen-md px-6 py-16 lg:px-8">
        <div class="panel p-8 sm:p-12">
            <p class="section-eyebrow mb-4">Ochrana osobních údajů</p>
            <h1 class="font-headline text-4xl text-on-surface dark:text-[#e5e2dd]">GDPR</h1>
            <div class="prose prose-stone mt-8 max-w-none dark:prose-invert">
                {{-- existing GDPR content --}}
            </div>
        </div>
    </div>
</x-site-layout>
```

- [ ] **Step 2: Run the full design render test suite**

```bash
php artisan test tests/Feature/DesignRenderTest.php
```

Expected: all tests PASS.

- [ ] **Step 3: Run full build**

```bash
npm run build 2>&1
```

Expected: build succeeds with no errors.

- [ ] **Step 4: Commit**

```bash
git add resources/views/gdpr.blade.php
git commit -m "feat: update GDPR page with panel layout

Co-Authored-By: Claude Sonnet 4.6 <noreply@anthropic.com>"
```

---

## Task 17: Push `frontend` branch

- [ ] **Step 1: Push all commits to the `frontend` branch on origin**

```bash
git push origin HEAD:frontend
```

Expected: all commits pushed, `frontend` branch on GitHub is up to date.

- [ ] **Step 2: Verify on GitHub**

```bash
gh pr create --base main --head frontend --title "Implement Heritage Field Stitch design system across all Blade views" --body "$(cat <<'EOF'
## Summary
- Add full Stitch colour token scale to Tailwind config with dark mode support
- Rebuild CSS component library (panel, buttons, fields, pills, glass-card)
- Update all three layouts (public, app, guest) with Newsreader font and glassmorphic nav
- Rebuild 5 key Stitch pages: events index, event detail, user dashboard, registration form, admin dashboard
- Replace all hardcoded colours in remaining views with token-based classes

## Test plan
- [ ] Run `php artisan test tests/Feature/DesignRenderTest.php` — all green
- [ ] Run `npm run build` — no errors
- [ ] Open `http://localhost:8086` — verify Heritage Field look in light mode
- [ ] Switch OS to dark mode — verify dark theme on all pages
- [ ] Navigate: events list → event detail → register → user dashboard → admin dashboard
- [ ] Test auth flow: register, login, forgot password

🤖 Generated with [Claude Code](https://claude.com/claude-code)
EOF
)"
```

---

## Self-Review Notes

- **Token naming:** All tasks use the exact token names from Task 2 (`primary`, `on-surface`, `secondary-container`, etc.) — consistent throughout.
- **Dark mode:** Every page section includes `dark:` variants; component classes in `app.css` include `.dark` overrides.
- **No TBD:** All code is fully written. Task 10 (registration form) intentionally requires a read step first because `_form.blade.php` is large (408 lines) with complex Alpine.js — writing a complete replacement blind would be error-prone.
- **Controller update:** Task 11 includes a DashboardController change — committed together with the view.
- **Image assets:** Replaced with CSS linear gradients throughout (`background: linear-gradient(135deg, #173809 ...)`).
- **Newsletter footer:** Rendered as disabled UI-only (no form action, button has `disabled` attribute).
