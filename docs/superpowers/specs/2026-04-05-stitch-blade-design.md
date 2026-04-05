# Stitch → Blade Design Implementation

**Date:** 2026-04-05
**Branch:** `frontend`
**Approach:** C — Token-first + component library

---

## 1. Overview

Apply the "Heritage Field" design system from the Stitch export to the entire EcolaCup Blade application. The dev PR (`bd7b29d`) already established a good semantic CSS class architecture (`.panel`, `.button-primary`, `.field-shell`, etc.) and the correct structural approach — this implementation replaces its design tokens, updates its component styles, rebuilds the 5 key Stitch pages in Blade, and ensures all remaining pages inherit a consistent look through the shared component library.

Dark mode is included, using Tailwind's `class` strategy with the Stitch dark-mode colour variants.

---

## 2. Design Tokens

### 2.1 tailwind.config.js

Add the full Stitch colour token scale as Tailwind custom colours. Keep existing config structure; replace/extend `colors` and update `fontFamily`.

**Colours to add (light mode values):**

| Token | Hex |
|---|---|
| `primary` | `#173809` |
| `on-primary` | `#ffffff` |
| `primary-container` | `#2d4f1e` |
| `on-primary-container` | `#98c083` |
| `primary-fixed` | `#c5efad` |
| `primary-fixed-dim` | `#a9d293` |
| `on-primary-fixed` | `#062100` |
| `on-primary-fixed-variant` | `#2d4f1e` |
| `secondary` | `#77574d` |
| `on-secondary` | `#ffffff` |
| `secondary-container` | `#fed3c7` |
| `on-secondary-container` | `#795950` |
| `secondary-fixed` | `#ffdbd0` |
| `secondary-fixed-dim` | `#e7bdb1` |
| `on-secondary-fixed` | `#2c160e` |
| `on-secondary-fixed-variant` | `#5d4037` |
| `tertiary` | `#422c0a` |
| `on-tertiary` | `#ffffff` |
| `tertiary-container` | `#5b421e` |
| `on-tertiary-container` | `#d2af82` |
| `tertiary-fixed` | `#ffddb3` |
| `tertiary-fixed-dim` | `#e5c192` |
| `on-tertiary-fixed` | `#291800` |
| `on-tertiary-fixed-variant` | `#5b421f` |
| `surface` | `#fcf9f4` |
| `surface-dim` | `#dcdad5` |
| `surface-bright` | `#fcf9f4` |
| `surface-container-lowest` | `#ffffff` |
| `surface-container-low` | `#f6f3ee` |
| `surface-container` | `#f0ede8` |
| `surface-container-high` | `#ebe8e3` |
| `surface-container-highest` | `#e5e2dd` |
| `surface-variant` | `#e5e2dd` |
| `surface-tint` | `#446733` |
| `on-surface` | `#1c1c19` |
| `on-surface-variant` | `#43493e` |
| `outline` | `#73796d` |
| `outline-variant` | `#c3c8bb` |
| `inverse-surface` | `#31302d` |
| `inverse-on-surface` | `#f3f0eb` |
| `inverse-primary` | `#a9d293` |
| `error` | `#ba1a1a` |
| `on-error` | `#ffffff` |
| `error-container` | `#ffdad6` |
| `on-error-container` | `#93000a` |
| `background` | `#fcf9f4` |
| `on-background` | `#1c1c19` |

**Border radius:**

| Key | Value |
|---|---|
| `DEFAULT` | `0.125rem` |
| `lg` | `0.25rem` |
| `xl` | `0.5rem` |
| `full` | `0.75rem` |

**Font families:**

```js
fontFamily: {
  headline: ['Newsreader', 'serif'],
  body: ['Manrope', 'sans-serif'],
  label: ['Manrope', 'sans-serif'],
  serif: ['Newsreader', 'serif'],   // utility alias
  sans: ['Manrope', 'sans-serif'],  // Tailwind default override
}
```

**Dark mode:** `darkMode: 'class'`

### 2.2 Google Fonts

Replace `Fraunces` with `Newsreader` in `site-layout.blade.php` and `guest.blade.php`:

```html
<link href="https://fonts.googleapis.com/css2?family=Newsreader:ital,opsz,wght@0,6..72,200..800;1,6..72,200..800&family=Manrope:wght@200..800&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
```

---

## 3. Dark Mode Strategy

Dark mode is toggled via `class` on `<html>`. No toggle UI is required in this implementation — the browser/OS preference can drive it via a small inline script. All Blade layouts add `dark` class based on a `prefers-color-scheme` media check on page load.

**Dark colour overrides** (applied as `dark:` Tailwind variants or CSS variables):

| Light token | Dark value |
|---|---|
| `background` | `#1c1c19` |
| `surface` | `#1c1c19` |
| `surface-container-low` | `#252522` |
| `surface-container` | `#2a2a27` |
| `surface-container-high` | `#313130` |
| `surface-container-highest` | `#3b3b38` |
| `on-surface` | `#e5e2dd` |
| `on-surface-variant` | `#c3c8bb` |
| `primary` | `#a9d293` |
| `on-primary` | `#0f2e06` |
| `primary-container` | `#2d4f1e` |
| `on-primary-container` | `#c5efad` |
| `secondary` | `#e7bdb1` |
| `secondary-container` | `#5d4037` |
| `on-secondary-container` | `#ffdbd0` |
| `outline` | `#8d9387` |
| `outline-variant` | `#43493e` |

Dark variants are applied inline via `dark:` classes in Blade views and component classes in `app.css`.

**OS-preference script** (added to `<head>` in both layouts):

```html
<script>
  if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
    document.documentElement.classList.add('dark');
  }
</script>
```

---

## 4. Component Library (app.css)

All existing class names are preserved for backward compatibility. Values are updated to use Stitch tokens.

### 4.1 Base

```css
body {
  background-color: theme('colors.background');
  color: theme('colors.on-surface');
  font-family: theme('fontFamily.body');
}
/* Remove radial-gradient background — replaced by flat background token */

h1, h2, h3, h4 {
  font-family: theme('fontFamily.headline');
  letter-spacing: 0.01em;
}
```

### 4.2 `.site-nav`

- Position: `fixed` (was `sticky`)
- Background: `bg-stone-50/80 dark:bg-stone-900/80 backdrop-blur-md`
- No bottom border — section separation is tonal only
- Logo: italic Newsreader "EcolaCup", `text-emerald-950 dark:text-emerald-100`

### 4.3 `.panel`

- Background: `surface-container-lowest` (`#ffffff`) / dark: `surface-container`
- Border: `outline-variant` at 20% opacity — kept as ghost border for accessibility
- Shadow: extra-diffuse, 40px blur, 8% opacity, tinted `on-surface`
- Border radius: `1.5rem` (unchanged)

### 4.4 `.button-primary`

- Background: `linear-gradient(135deg, #173809 0%, #2d4f1e 100%)`
- Text: `on-primary` white, all-caps, `tracking-widest`, `text-sm`
- Shape: `rounded-lg` (0.25rem — not pill)
- Hover: `translateY(-2px)`, deeper shadow
- Dark: same gradient, `dark:text-on-primary`

### 4.5 `.button-secondary`

- Background: `surface-container-highest` / dark: `surface-container-high`
- Text: `on-surface` / dark: `on-surface`
- No border
- Shape: `rounded-lg`

### 4.6 `.field-shell`

- Background: transparent
- Border: none on sides/top — 2px bottom border `outline-variant`
- Focus: bottom border transitions to `primary`; add `ring-0` to suppress browser default
- Error state: bottom border + label text → `error` token
- Dark: `outline-variant` dark value for border

### 4.7 `.brand-pill`

- Background: `secondary-container` / dark: `secondary-container` dark
- Text: `on-secondary-container` / dark: `on-secondary-container` dark
- Shape: `rounded-full` (pill, unchanged)

### 4.8 `.section-eyebrow`

- Color: `primary` / dark: `inverse-primary`
- Uppercase, wide tracking (unchanged)

### 4.9 `.site-chroma`

Remove — no longer needed with flat surface token background.

### 4.10 New utility classes

```css
/* Stitch gradient CTA — alias for button-primary gradient */
.btn-gradient {
  background: linear-gradient(135deg, theme('colors.primary') 0%, theme('colors.primary-container') 100%);
}

/* Glassmorphism floating card (registration summary, modals) */
.glass-card {
  background: rgba(252, 249, 244, 0.85);
  backdrop-filter: blur(16px);
  border: 1px solid rgba(195, 200, 187, 0.2);
}
.dark .glass-card {
  background: rgba(28, 28, 25, 0.85);
  border-color: rgba(67, 73, 62, 0.3);
}
```

---

## 5. Shared Layouts

### 5.1 `site-layout.blade.php`

**Nav structure:**
- Fixed, glassmorphic (`stone-50/80 dark:stone-900/80 backdrop-blur-md`)
- Left: italic Newsreader logo "EcolaCup"
- Centre (md+): Události | GDPR | Moje přihlášky | Admin (auth-guarded)
- Right: Login link (guest) or "Otevřít aplikaci" gradient button (auth)
- Active link: 2px bottom border in `primary`

**Footer structure:**
- `stone-100 dark:stone-950`, `rounded-t-3xl`
- 3-column grid: brand blurb + icon links | quick links (2-col sub-grid) | newsletter email input
- Newsletter: `field-shell` input + `button-primary` send icon button

**Body:**
- `<html>` gets dark mode script in `<head>`
- `body` class: `bg-background text-on-surface antialiased` (remove `site-shell`)
- Remove `.site-chroma` div

### 5.2 `guest.blade.php` (auth pages)

- Full-screen `bg-background` with centred glass card
- Card: `glass-card` class, max-w-md, `rounded-xl`
- Logo at top of card: italic Newsreader "EcolaCup"
- No sidebar image (keep clean, centred)

---

## 6. Page Implementations

### 6.1 `udalosti/index.blade.php` — Landing + Race Discovery

**Sections (from `landing_page` + `race_discovery` Stitch screens):**

1. **Hero** — asymmetric 12-col grid. Left 6 cols: display headline (Newsreader, 6xl–8xl), subtitle, two CTA buttons. Right 6–7 cols: large image with organic border-radius (`rounded-l-[4rem]`), `primary/10` overlay.
2. **Stats bar** — 3 inline stats (upcoming count, open registrations, archive count).
3. **Upcoming Races bento grid** — 4-col grid, 2-row. Featured card spans 2×2 (image top 2/3, details below). Two small horizontal cards. One dark "Nejbližší" card. One stat-only card.
4. **Archive section** — eyebrow label, card list using `surface-container-low` alternating rows (no dividers).
5. **CTA banner** — full-width glassmorphic section over background image, headline + two buttons.

**Data bindings:** `$upcoming`, `$archive`, `$openEvents` (unchanged from controller).

### 6.2 `udalosti/show.blade.php` — Race Detail

**Sections (from `race_detail_registration` Stitch screen):**

1. **Hero** — asymmetric. Left 3/5: eyebrow chip (discipline type), large Newsreader display headline (event name), subtitle (location/date), two CTAs (Register / View Details). Right 2/5: organic-clipped image.
2. **Stats bento grid** — 4-col. Two metric cards (`surface-container-low`): capacity/distance stats. One dark `primary` card: difficulty/status. One schedule list card.
3. **Requirements & Details** — 2-col. Left: entry requirements list. Right: disciplines table (no borders — tonal row alternation).
4. **Ustajení** — card grid using `surface-container-highest` cards.
5. **Registration CTA** — sticky-capable sidebar card or inline CTA block with `glass-card` and gradient button.

**Data bindings:** `$udalost`, `$udalost->moznosti`, `$udalost->ustajeniMoznosti`.

### 6.3 `prihlasky/index.blade.php` — User Dashboard

**Sections (from `user_dashboard` Stitch screen):**

1. **Header** — "Moje přihlášky" headline + "Nová přihláška" gradient CTA.
2. **Metrics bento** — 4-col. Active registrations count. Upcoming events count. Wide card: next event highlight with date.
3. **Registration list** — card-based (no table). Each card: event image thumbnail, event name + date, status chip (`.brand-pill`), discipline tags, action buttons (Detail / Edit / PDF).
4. **Quick links** — small card row: Moje koně | Moje osoby | CMT členství.

**Data bindings:** `$prihlasky` (from `PrihlaskaController::index`).

### 6.4 `prihlasky/_form.blade.php` — Registration Form

**Design rules (from `paddock_reserve` DESIGN.md — no code.html):**

- No border-boxed sections — tonal background shifts define form sections
- Section backgrounds: `surface-container-low` → `surface-container` → `surface-container-high` nesting
- All inputs use `.field-shell` (bottom-border only)
- Section headers: Newsreader serif, `on-surface`
- Sticky registration summary sidebar (`glass-card`) on desktop
- Status/info blocks: `.status-note` with `secondary-container` background
- Error messages: `error` token, no border box

### 6.5 `admin/dashboard.blade.php` — Organiser Dashboard

**Sections (from `event_management` Stitch screen):**

1. **Header** — italic Newsreader "Přehled administrace" + "Vytvořit akci" gradient CTA.
2. **Metrics bento** — 4-col. Active events. Total registrations. Wide `primary-container` card: revenue/fee summary with progress bar.
3. **Upcoming events list** — card-based (no table). Each event: thumbnail, name + date + location, registered/capacity counts, Edit + Reports buttons.

**Data bindings:** admin dashboard controller data (unchanged).

---

## 7. Pages Inheriting the System (no Stitch mockup)

These pages are not rebuilt from Stitch HTML but adopt the component library uniformly:

| Page group | Key changes |
|---|---|
| `kone/*` | `.panel` list cards, `.field-shell` inputs, `.button-primary` / `.button-secondary`, `.brand-pill` status |
| `osoby/*` | Same as kone |
| `clenstvi-cmt/*` | Status chip via `.brand-pill` with `secondary-container` colours; renewal CTA as `.button-primary` |
| `prihlasky/show.blade.php` | Detail card in `.panel`, discipline list tonal rows, PDF button `.button-secondary` |
| `prihlasky/create.blade.php` / `edit.blade.php` | Wrap `_form.blade.php` in tonal layout — no changes to form partials beyond what §6.4 covers |
| `profile/*` | `.panel` sections, `.field-shell` inputs |
| `admin/udalosti/*` | `.panel` form layout, tonal section separation, `.field-shell` inputs |
| `admin/reports/*` | Tonal alternating rows (no dividers), `.brand-pill` status chips, export `.button-secondary` |
| `admin/users/*` | Same table pattern as reports |
| `auth/*` | `guest.blade.php` glass card layout |
| `gdpr.blade.php` | Wrapped in `site-layout`, prose content in `.panel` |

---

## 8. Implementation Sequence

Execute in this order to keep the app functional at each step:

1. **Create `frontend` branch** from current `main`
2. **Tailwind config** — add Stitch colour tokens, font families, border radius, `darkMode: 'class'`
3. **app.css** — rebuild component classes with new tokens; remove `.site-chroma`
4. **Layouts** — update `site-layout.blade.php` and `guest.blade.php` (font, nav, footer, dark script)
5. **udalosti/index** — landing + race discovery rebuild
6. **udalosti/show** — race detail rebuild
7. **prihlasky/index** — user dashboard rebuild
8. **prihlasky/_form** — registration form rebuild
9. **admin/dashboard** — organiser dashboard rebuild
10. **Remaining pages** — kone, osoby, clenstvi-cmt, prihlasky/show, profile, auth, admin CRUD, reports, gdpr

---

## 9. Out of Scope

- Dark mode toggle UI (OS preference only)
- Image assets — Stitch uses Google AI-generated images; production will use placeholder or user-uploaded images
- New backend routes or domain/data-contract changes — minimal controller or view-model adjustments are acceptable when required to render the redesigned Blade pages
- Admin start-cisla and GDPR export pages — adopt component library but no layout redesign
