# KONEAKCE — New Website Build Plan
**Horse Events & Race Registration Platform**
**Framework:** PHP Laravel · **Database:** MySQL · **Server:** Ubuntu Linux + Apache

---

## Table of Contents

1. [Project Overview](#1-project-overview)
2. [Technology Stack](#2-technology-stack)
3. [Server & Environment Setup](#3-server--environment-setup)
4. [Database Design](#4-database-design)
5. [Backend — Stage-by-Stage Plan](#5-backend--stage-by-stage-plan)
6. [Frontend — Stage-by-Stage Plan](#6-frontend--stage-by-stage-plan)
7. [Mock Data & Seeding](#7-mock-data--seeding)
8. [Admin Panel](#8-admin-panel)
9. [PDF, Excel & Email Engine](#9-pdf-excel--email-engine)
10. [Security & GDPR](#10-security--gdpr)
11. [Deployment Checklist](#11-deployment-checklist)
12. [Project File Structure](#12-project-file-structure)

---

## 1. Project Overview

**koneakce.cz** is a Czech-language web platform for managing horse trail competition events (primarily **Czech Mountain Trail / CMT**). The new build replaces the legacy PHP flat-file site with a modern, maintainable Laravel application backed by MySQL.

### Core Modules

| Module | Description |
|---|---|
| Auth | Registration, login, password reset, session management |
| Persons (Osoby) | Riders/handlers managed per user account |
| Horses (Koně) | Horse profiles with breed, health records, passport data |
| Events (Události) | Event listing, archive, detail, discipline/stabling options |
| Registrations (Přihlášky) | Full registration workflow with disciplines, stabling, tandem |
| CMT Membership (Členství) | Club membership registration and annual renewal |
| Admin Reports | Excel exports, start lists, stabling reports |
| PDF Engine | Per-registration printable PDF, blank form, bulk PDF |

### Design Goals

- Replace dated pastel-table layout with a **modern, responsive design**
- Keep the Czech-language UX familiar to existing users
- Full mobile responsiveness (users register on phones at events)
- Clean card-based UI, horse-themed colour palette (earthy greens, browns, cream)
- Accessible form flows with live validation (Alpine.js or vanilla JS)

---

## 2. Technology Stack

| Layer | Technology | Version |
|---|---|---|
| Language | PHP | 8.2+ |
| Framework | Laravel | 11.x |
| Database | MySQL | 8.0+ |
| Web Server | Apache | 2.4 |
| OS | Ubuntu | 22.04 LTS |
| CSS Framework | Tailwind CSS | 3.x |
| JS | Alpine.js + Axios | 3.x / 1.x |
| PDF | Laravel DomPDF (barryvdh/laravel-dompdf) | latest |
| Excel | Laravel Excel (maatwebsite/excel) | 3.x |
| Email | Laravel Mail + SMTP (Mailable classes) | built-in |
| Auth | Laravel Breeze (Blade stack) | latest |
| Queue | Laravel Queue (database driver) | built-in |
| Asset Build | Vite | built-in with Laravel 11 |

---

## 3. Server & Environment Setup

### 3.1 Ubuntu + Apache Installation

```bash
# System update
sudo apt update && sudo apt upgrade -y

# Install Apache
sudo apt install apache2 -y
sudo a2enmod rewrite
sudo systemctl enable apache2

# Install PHP 8.2 and required extensions
sudo apt install php8.2 php8.2-cli php8.2-fpm php8.2-mysql \
  php8.2-xml php8.2-mbstring php8.2-curl php8.2-zip \
  php8.2-bcmath php8.2-intl php8.2-gd -y

# Install MySQL
sudo apt install mysql-server -y
sudo mysql_secure_installation

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js (for Vite asset building)
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install nodejs -y
```

### 3.2 Apache Virtual Host

```apache
# /etc/apache2/sites-available/koneakce.conf

<VirtualHost *:80>
    ServerName koneakce.cz
    ServerAlias www.koneakce.cz
    DocumentRoot /var/www/koneakce/public

    <Directory /var/www/koneakce/public>
        AllowOverride All
        Require all granted
        Options -Indexes +FollowSymLinks
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/koneakce_error.log
    CustomLog ${APACHE_LOG_DIR}/koneakce_access.log combined
</VirtualHost>
```

```bash
sudo a2ensite koneakce.conf
sudo systemctl reload apache2
```

### 3.3 Laravel Project Initialisation

```bash
cd /var/www
composer create-project laravel/laravel koneakce
cd koneakce

# Install auth scaffolding
composer require laravel/breeze --dev
php artisan breeze:install blade

# Install packages
composer require barryvdh/laravel-dompdf
composer require maatwebsite/excel
composer require intervention/image

# Install frontend deps and build
npm install
npm run build

# Set permissions
sudo chown -R www-data:www-data /var/www/koneakce
sudo chmod -R 755 /var/www/koneakce/storage
sudo chmod -R 755 /var/www/koneakce/bootstrap/cache
```

### 3.4 Environment Configuration (.env)

```dotenv
APP_NAME="Koně Akce Přihlášky"
APP_ENV=production
APP_URL=https://koneakce.cz
APP_LOCALE=cs
APP_TIMEZONE=Europe/Prague

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=koneakce
DB_USERNAME=koneakce_user
DB_PASSWORD=strong_password_here

MAIL_MAILER=smtp
MAIL_HOST=smtp.your-provider.cz
MAIL_PORT=587
MAIL_USERNAME=prihlaska@koneakce.cz
MAIL_PASSWORD=mail_password_here
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=prihlaska@koneakce.cz
MAIL_FROM_NAME="Koně Akce Přihlášky"

QUEUE_CONNECTION=database
```

### 3.5 MySQL Database Setup

```sql
CREATE DATABASE koneakce CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'koneakce_user'@'localhost' IDENTIFIED BY 'strong_password_here';
GRANT ALL PRIVILEGES ON koneakce.* TO 'koneakce_user'@'localhost';
FLUSH PRIVILEGES;
```

---

## 4. Database Design

### Entity-Relationship Overview

```
users ──< osoby (persons) ──< clenstvi_cmt (CMT memberships)
  │             └──< prihlasky (registrations)
  └──< kone (horses) ──< prihlasky
                              │
udalosti (events) ──< prihlasky
     └──< udalost_moznosti (disciplines)
     └──< udalost_ustajeni (stabling options)
               └──< prihlasky_ustajeni
                         │
                   prihlasky_polozky (selected disciplines)
```

---

### 4.1 Migration: `users`

```php
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('jmeno');                    // first name
    $table->string('prijmeni');                 // last name
    $table->date('datum_narozeni')->nullable();
    $table->enum('pohlavi', ['M', 'F'])->nullable();
    $table->string('email')->unique();
    $table->string('telefon')->nullable();
    $table->timestamp('email_verified_at')->nullable();
    $table->string('password');
    $table->boolean('is_admin')->default(false);
    $table->boolean('gdpr_souhlas')->default(false);
    $table->timestamp('gdpr_souhlas_at')->nullable();
    $table->rememberToken();
    $table->timestamps();
    $table->softDeletes();
});
```

---

### 4.2 Migration: `osoby` (Persons / Riders)

```php
Schema::create('osoby', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->string('jmeno');
    $table->string('prijmeni');
    $table->date('datum_narozeni');
    $table->string('staj');                     // stable / team name
    $table->boolean('gdpr_souhlas')->default(false);
    $table->boolean('gdpr_odvolano')->default(false);
    $table->timestamp('gdpr_souhlas_at')->nullable();
    $table->timestamps();
    $table->softDeletes();
});
```

---

### 4.3 Migration: `kone` (Horses)

```php
Schema::create('kone', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->string('jmeno');                    // official name from passport
    $table->string('plemeno_kod', 20)->nullable();   // breed abbreviation
    $table->string('plemeno_nazev')->nullable();     // breed name
    $table->string('plemeno_vlastni')->nullable();   // manual breed override
    $table->year('rok_narozeni');
    $table->string('staj');
    $table->enum('pohlavi', ['h', 'k', 'v']);   // stallion/mare/gelding
    $table->date('ehv_datum')->nullable();       // EHV vaccination
    $table->date('aie_datum')->nullable();       // AIE test
    $table->date('chripka_datum')->nullable();   // influenza vaccination
    $table->string('cislo_prukazu')->nullable(); // passport number
    $table->string('cislo_hospodarstvi')->nullable(); // farm number
    $table->text('majitel_jmeno_adresa')->nullable(); // owner from passport
    $table->timestamps();
    $table->softDeletes();
});
```

---

### 4.4 Migration: `udalosti` (Events)

```php
Schema::create('udalosti', function (Blueprint $table) {
    $table->id();
    $table->string('nazev');                    // event name
    $table->string('misto');                    // location
    $table->date('datum_zacatek');
    $table->date('datum_konec');
    $table->date('uzavierka_prihlasek');        // registration deadline
    $table->integer('kapacita')->nullable();    // null = unlimited
    $table->string('propozice_pdf')->nullable(); // path to uploaded regulations PDF
    $table->boolean('aktivni')->default(true);
    $table->text('popis')->nullable();
    $table->timestamps();
    $table->softDeletes();
});
```

---

### 4.5 Migration: `udalost_moznosti` (Event Disciplines)

```php
Schema::create('udalost_moznosti', function (Blueprint $table) {
    $table->id();
    $table->foreignId('udalost_id')->constrained('udalosti')->cascadeOnDelete();
    $table->string('nazev');                    // discipline name
    $table->integer('min_vek')->nullable();     // minimum age
    $table->decimal('cena', 8, 2);             // price in Kč
    $table->integer('poradi')->default(0);     // display order
    $table->boolean('je_administrativni_poplatek')->default(false);
    $table->timestamps();
});
```

---

### 4.6 Migration: `udalost_ustajeni` (Stabling Options)

```php
Schema::create('udalost_ustajeni', function (Blueprint $table) {
    $table->id();
    $table->foreignId('udalost_id')->constrained('udalosti')->cascadeOnDelete();
    $table->string('nazev');                    // e.g. "Vnitřní boxy 18m2"
    $table->enum('typ', ['ustajeni', 'ubytovani', 'strava', 'ostatni']);
    $table->decimal('cena', 8, 2);
    $table->integer('kapacita')->nullable();    // null = unlimited
    $table->timestamps();
});
```

---

### 4.7 Migration: `prihlasky` (Registrations)

```php
Schema::create('prihlasky', function (Blueprint $table) {
    $table->id();
    $table->foreignId('udalost_id')->constrained('udalosti');
    $table->foreignId('user_id')->constrained('users');
    $table->foreignId('osoba_id')->constrained('osoby');
    $table->foreignId('kun_id')->constrained('kone');
    $table->foreignId('kun_tandem_id')->nullable()->constrained('kone');
    $table->integer('start_cislo')->nullable();
    $table->text('poznamka')->nullable();
    $table->boolean('gdpr_souhlas')->default(false);
    $table->decimal('cena_celkem', 10, 2)->default(0);
    $table->boolean('smazana')->default(false); // soft delete flag (business logic)
    $table->timestamps();
    $table->softDeletes();                      // Laravel soft delete
});
```

---

### 4.8 Migration: `prihlasky_polozky` (Registration Discipline Items)

```php
Schema::create('prihlasky_polozky', function (Blueprint $table) {
    $table->id();
    $table->foreignId('prihlaska_id')->constrained('prihlasky')->cascadeOnDelete();
    $table->foreignId('moznost_id')->constrained('udalost_moznosti');
    $table->string('nazev');                    // snapshot of name at time of registration
    $table->decimal('cena', 8, 2);             // snapshot of price
    $table->timestamps();
});
```

---

### 4.9 Migration: `prihlasky_ustajeni` (Registration Stabling Choices)

```php
Schema::create('prihlasky_ustajeni', function (Blueprint $table) {
    $table->id();
    $table->foreignId('prihlaska_id')->constrained('prihlasky')->cascadeOnDelete();
    $table->foreignId('ustajeni_id')->constrained('udalost_ustajeni');
    $table->decimal('cena', 8, 2);
    $table->timestamps();
});
```

---

### 4.10 Migration: `clenstvi_cmt` (CMT Memberships)

```php
Schema::create('clenstvi_cmt', function (Blueprint $table) {
    $table->id();
    $table->foreignId('osoba_id')->constrained('osoby')->cascadeOnDelete();
    $table->foreignId('organizace_id')->default(2); // CMT org ID
    $table->string('evidencni_cislo', 20)->nullable();
    $table->string('titul')->nullable();
    $table->string('bydliste')->nullable();
    $table->string('telefon')->nullable();
    $table->string('email')->nullable();
    // legal entity
    $table->string('nazev_organizace')->nullable();
    $table->string('ico')->nullable();
    // membership type
    $table->enum('typ_clenstvi', ['fyzicka_osoba', 'mladez', 'pravnicka_osoba']);
    $table->year('rok');
    $table->decimal('cena', 8, 2);
    $table->boolean('aktivni')->default(false);
    // guardian (for minors)
    $table->string('zastupce_titul')->nullable();
    $table->string('zastupce_jmeno')->nullable();
    $table->string('zastupce_prijmeni')->nullable();
    $table->year('zastupce_rok_narozeni')->nullable();
    $table->string('zastupce_vztah')->nullable();
    $table->string('zastupce_bydliste')->nullable();
    $table->string('zastupce_telefon')->nullable();
    $table->string('zastupce_email')->nullable();
    // scanned form
    $table->string('sken_prihlaska')->nullable();
    // consents
    $table->boolean('souhlas_gdpr')->default(false);
    $table->boolean('souhlas_email')->default(false);
    $table->boolean('souhlas_zverejneni')->default(false);
    $table->timestamps();
});
```

---

### 4.11 Migration: `plemena` (Horse Breeds — Lookup Table)

```php
Schema::create('plemena', function (Blueprint $table) {
    $table->id();
    $table->string('kod', 20)->unique();        // e.g. "WPB"
    $table->string('nazev');                    // e.g. "Welsh part bred"
    $table->integer('poradi')->default(0);
});
```

---

## 5. Backend — Stage-by-Stage Plan

---

### STAGE B1 — Project Scaffold & Auth  *(Week 1)*

**Goal:** Working Laravel install with user registration, login, logout, password reset.

**Tasks:**
- [ ] Laravel 11 project creation, Breeze install
- [ ] Custom user migration (add `jmeno`, `prijmeni`, `datum_narozeni`, `pohlavi`, `telefon`, `gdpr_souhlas`)
- [ ] Extend `RegisteredUserController` to handle extra fields
- [ ] `UserController` — edit account (`GET /ucet/edit`, `PUT /ucet`)
- [ ] Email verification (optional, enable in `AuthServiceProvider`)
- [ ] Middleware: `auth` guard on all protected routes
- [ ] `AdminMiddleware` — gate all `/admin/*` routes to `is_admin = true`

**Routes:**
```
GET  /                → redirect to /udalosti (guest) or dashboard
GET  /registrace      → show registration form
POST /registrace      → store new user
GET  /prihlaseni      → show login form
POST /prihlaseni      → authenticate
POST /odhlaseni       → logout
GET  /ucet/edit       → edit account form
PUT  /ucet            → update account
GET  /ucet/heslo      → change password form
PUT  /ucet/heslo      → update password
```

---

### STAGE B2 — Persons (Osoby) Module  *(Week 1–2)*

**Goal:** Full CRUD for rider/handler profiles.

**Tasks:**
- [ ] `Osoba` model with `belongsTo(User)`, fillable fields, SoftDeletes
- [ ] `OsobaController` — index, create, store, edit, update, destroy
- [ ] Policy: `OsobaPolicy` — users can only manage their own persons
- [ ] CMT status accessor (derived from `clenstvi_cmt` relationship)
- [ ] GDPR revocation logic on update

**Routes:**
```
GET    /osoby            → list user's persons
GET    /osoby/nova       → create form
POST   /osoby            → store
GET    /osoby/{id}/edit  → edit form
PUT    /osoby/{id}       → update
DELETE /osoby/{id}       → soft-delete
```

---

### STAGE B3 — Horses (Koně) Module  *(Week 2)*

**Goal:** Full CRUD for horse profiles with breed lookup.

**Tasks:**
- [ ] `Kun` model with `belongsTo(User)`, SoftDeletes
- [ ] `Pleme` model seeded with all 50+ breeds
- [ ] `KunController` — full CRUD
- [ ] Policy: `KunPolicy` — own horses only
- [ ] Vaccination expiry warning logic (accessor: `ockovani_ok` — returns array of expired/missing dates)
- [ ] Validation: passport number format check

**Routes:**
```
GET    /kone             → list user's horses
GET    /kone/novy        → create form
POST   /kone             → store
GET    /kone/{id}/edit   → edit form
PUT    /kone/{id}        → update
DELETE /kone/{id}        → soft-delete
```

---

### STAGE B4 — Events (Události) Module  *(Week 2–3)*

**Goal:** Admin creates events; public views list, archive, detail.

**Tasks:**
- [ ] `Udalost` model with `hasMany(UdalostMoznost)`, `hasMany(UdalostUstajeni)`, `hasMany(Prihlaska)`
- [ ] `UdalostMoznost` model
- [ ] `UdalostUstajeni` model
- [ ] `UdalostController` — public routes (index, archive, show)
- [ ] `Admin\UdalostController` — full CRUD + discipline/stabling management
- [ ] PDF upload for regulations (`propozice`) stored in `storage/app/public/propozice`
- [ ] Computed: `pocet_prihlasek` (count active registrations), `pocet_startu` (count discipline items)
- [ ] Capacity check: when `kapacita` is set, block new registrations when full

**Public Routes:**
```
GET  /udalosti                    → upcoming events + archive
GET  /udalosti/{id}               → event detail
GET  /udalosti/{id}/prihlasit     → new registration form (auth required)
```

**Admin Routes:**
```
GET    /admin/udalosti             → list all events
GET    /admin/udalosti/nova        → create form
POST   /admin/udalosti             → store
GET    /admin/udalosti/{id}/edit   → edit form
PUT    /admin/udalosti/{id}        → update
DELETE /admin/udalosti/{id}        → soft-delete
POST   /admin/udalosti/{id}/moznosti         → add discipline
DELETE /admin/udalosti/{id}/moznosti/{mid}   → remove discipline
POST   /admin/udalosti/{id}/ustajeni         → add stabling option
DELETE /admin/udalosti/{id}/ustajeni/{uid}   → remove stabling option
```

---

### STAGE B5 — Registration (Přihláška) Module  *(Week 3–4)*

**Goal:** Full registration workflow with live AJAX updates, business logic, email confirmation.

**Tasks:**
- [ ] `Prihlaska` model with all relationships, `SoftDeletes`
- [ ] `PrihlaskaController` — store, show, edit, update, destroy
- [ ] Business logic service: `PrihlaskaService`
  - Admin fee deduplication (one admin fee per person per event)
  - Total price recalculation
  - Start number auto-assignment
  - Deadline enforcement (read-only after `uzavierka_prihlasek`)
- [ ] AJAX endpoints (JSON responses):
  - `GET /ajax/osoba/{id}/polozky?udalost={id}` — discipline items + admin fee check
  - `GET /ajax/osoba/{id}/clenstvi-img?udalost={id}` — CMT status badge
  - `GET /ajax/kun/{id}/ockovani` — vaccination dates
  - `GET /ajax/udalost/{id}/admin-poplatek?osoba={id}` — admin fee already charged?
- [ ] `PrihlaskaMail` Mailable — sends PDF attachment on registration
- [ ] Queue dispatch: `SendPrihlaskaEmail` job dispatched after save
- [ ] Edit restrictions: person and primary horse read-only after creation

**Routes:**
```
POST   /udalosti/{id}/prihlasit          → store registration
GET    /prihlasky                        → archive of user's registrations
GET    /prihlasky/{id}                   → view single registration
GET    /prihlasky/{id}/edit              → edit registration (before deadline)
PUT    /prihlasky/{id}                   → update registration
DELETE /prihlasky/{id}                   → soft-delete (business flag)
GET    /prihlasky/{id}/pdf               → download registration PDF
```

---

### STAGE B6 — CMT Membership Module  *(Week 4–5)*

**Goal:** New member registration and annual renewal.

**Tasks:**
- [ ] `ClenstviCmt` model with `belongsTo(Osoba)`
- [ ] `ClenstviController` — show my memberships, create, renew
- [ ] Membership type + year + price matrix (seeded in config or DB)
- [ ] 100 Kč new-member admin fee auto-added on first registration
- [ ] Renewal creates new `clenstvi_cmt` record for next year (same person)
- [ ] AJAX: person selection auto-fills form fields
  - `GET /ajax/osoba/{id}/clenstvi-data` — returns person fields as JSON
- [ ] Scanned form image upload (stored in `storage/app/public/clenstvi`)
- [ ] Admin: mark membership as active/inactive, upload scan

**Routes:**
```
GET   /clenstvi           → my CMT memberships
GET   /clenstvi/nova      → new membership form
POST  /clenstvi           → store membership
POST  /clenstvi/{id}/prodlouzit  → renew for next year
```

---

### STAGE B7 — Admin Reports Module  *(Week 5)*

**Goal:** Organiser-facing reports and downloads.

**Tasks:**
- [ ] `Admin\ReportController`
- [ ] Excel exports using `maatwebsite/excel` Exports:
  - `SeznamPrihlasenych` — registration list (name, horse, breed, year, staj, price)
  - `DisciplinyExport` — participants and their disciplines
  - `EmailyExport` — email list
  - `VetPrejimkaExport` — horses for vet check
  - `UstajeniExport` — stabling/accommodation/catering breakdown
  - `StartkyExport` — start numbers by discipline
- [ ] `PrihlaskasPdf` — bulk PDF (all registrations in one file via DOMPDF + zip)
- [ ] `PrazdnaPrihlaskaPdf` — blank registration form PDF
- [ ] Start number management (auto-assign on registration, admin can override)
- [ ] Age category logic: youth (m), junior (j), led child under 8 (m*)

**Admin Report Routes:**
```
GET  /admin/udalosti/{id}/prihlasky              → registration list view
GET  /admin/udalosti/{id}/prihlasky/export/seznam        → XLS download
GET  /admin/udalosti/{id}/prihlasky/export/discipliny    → XLS download
GET  /admin/udalosti/{id}/prihlasky/export/emaily        → XLS download
GET  /admin/udalosti/{id}/prihlasky/export/kone          → XLS download
GET  /admin/udalosti/{id}/prihlasky/export/bulk-pdf      → ZIP of all PDFs
GET  /admin/udalosti/{id}/prihlasky/smazane              → deleted registrations
GET  /admin/udalosti/{id}/startky                        → start list by discipline
GET  /admin/udalosti/{id}/startky/export                 → XLS download
GET  /admin/udalosti/{id}/ubytovani                      → stabling/accommodation view
GET  /admin/udalosti/{id}/ubytovani/export               → XLS download
```

---

### STAGE B8 — API Hardening & Queue Workers  *(Week 5–6)*

**Tasks:**
- [ ] Configure `database` queue driver and run `php artisan queue:table`
- [ ] Supervisor config for queue worker on Ubuntu:

```ini
# /etc/supervisor/conf.d/koneakce-worker.conf
[program:koneakce-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/koneakce/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/koneakce/storage/logs/worker.log
```

- [ ] `php artisan schedule:run` via cron (for future scheduled tasks):

```bash
* * * * * cd /var/www/koneakce && php artisan schedule:run >> /dev/null 2>&1
```

- [ ] Rate limiting on registration and login routes (Laravel `throttle` middleware)
- [ ] CSRF protection on all forms (default in Laravel)
- [ ] Input sanitisation and validation rules on all form requests (`FormRequest` classes)

---

## 6. Frontend — Stage-by-Stage Plan

---

### Design Language

**Colour Palette** (replacing the dated pastel rainbow tabs):

| Token | Hex | Usage |
|---|---|---|
| `brand-green` | `#3D6B4F` | Primary buttons, active nav |
| `brand-brown` | `#7B5230` | Accent, headings |
| `brand-cream` | `#F5F0E8` | Page background |
| `brand-sand` | `#D4B896` | Cards, sidebar background |
| `brand-dark` | `#1C1C1C` | Body text |
| `success` | `#4CAF50` | Valid/active badges |
| `warning` | `#F59E0B` | Deadline warnings |
| `danger` | `#EF4444` | Delete, errors |

**Typography:**
- Headings: `Inter` or `Nunito` (Google Fonts)
- Body: system UI stack

**Component Library:** Tailwind CSS + custom components (no heavy UI framework dependency)

---

### STAGE F1 — Layout & Navigation  *(Week 2)*

**Goal:** Base layout that all pages extend.

**Blade Templates:**
```
resources/views/
  layouts/
    app.blade.php          ← authenticated layout
    guest.blade.php        ← login/register layout
    admin.blade.php        ← admin panel layout
  components/
    nav-link.blade.php
    sidebar-event-card.blade.php
    flash-message.blade.php
    badge-cmt.blade.php    ← CMT status badge (active/inactive/none)
    horse-card.blade.php
    registration-card.blade.php
```

**Top Navigation (authenticated):**
```
[Logo/Horse Icon]  Události  Přihlášky  Osoba  Kůň  Členství CMT  [User Avatar ▾]
                                                                    ├ Můj účet
                                                                    ├ Změnit heslo
                                                                    └ Odhlásit
```

**Left Sidebar (on main pages):**
- Upcoming Events card (next 3 events with date badges)
- My Open Registrations (horse + event quick links)
- My Account quick links

**Responsive Behaviour:**
- Top nav collapses to hamburger menu on mobile
- Sidebar becomes a bottom tab bar or collapsible drawer on mobile

---

### STAGE F2 — Auth Pages  *(Week 2)*

**Pages:** Login, Register, Forgot Password, Reset Password

**Design:**
- Centred card on cream background with horse-themed hero image strip
- Form fields with floating labels (Tailwind custom component)
- Password strength indicator on registration
- GDPR consent checkbox with expandable text

---

### STAGE F3 — Events Pages  *(Week 2–3)*

**Upcoming Events (`/udalosti`):**
- Hero banner with horse trail imagery
- Grid of event cards: event name, date badge, location, discipline count, registration count, CTA button
- Colour-coded deadline badge: green (open), amber (closing soon <7 days), red (closed)

**Event Archive:**
- Timeline layout grouped by year, collapsible by year
- Search/filter bar (by year, location, name)

**Event Detail (`/udalosti/{id}`):**
- Hero card with event name, date, location
- Info grid: deadline, capacity used (progress bar), start count
- Disciplines table with prices
- Stabling options table with availability
- "Přihlásit se na tuto akci" CTA button (disabled + tooltip if deadline passed or capacity full)
- "Propozice" PDF download button

---

### STAGE F4 — Registration Form  *(Week 3–4)*

This is the most complex UI page. Replace the old flat form with a **multi-step wizard** (3 steps, all on one page using Alpine.js `x-show`).

**Step 1 — Osoba & Kůň selection:**
- Person dropdown → on change, AJAX updates CMT badge + admin fee notice
- Primary horse dropdown → on change, AJAX shows vaccination status chips
- Tandem horse dropdown (hidden until tandem discipline selected)
- Vaccination warning banners (amber for expired, red for missing)

**Step 2 — Stabling & Disciplines:**
- Stabling options as visual toggle cards with price and availability bar
- Discipline checkboxes as styled item cards (icon + name + price)
- Running price total updates live in sidebar as items are checked/unchecked
- Admin fee shown with info tooltip (auto-managed, greyed out if already charged)

**Step 3 — Summary & Submit:**
- Full summary table (read-only): person, horse, selected items, stabling, total price
- Poznámka textarea
- GDPR radio buttons
- Submit button with loading spinner

**Price ticker** (sticky bottom bar on mobile / sidebar on desktop):
```
Vybrané položky: 3   Celkem: 900 Kč   [Odeslat přihlášku]
```

---

### STAGE F5 — Persons & Horses CRUD Pages  *(Week 3)*

**Persons List (`/osoby`):**
- Table → responsive cards on mobile
- CMT status badge per row (colour-coded: green active, amber inactive, grey none)
- Inline delete with confirmation modal

**Horse List (`/kone`):**
- Card grid (horse name, breed, year, stable)
- Vaccination status chips (EHV / AIE / Chřipka) — green if valid, red if expired/missing
- Passport completeness indicator

**Add/Edit Forms:**
- Breed: searchable select2-style dropdown (Alpine.js + Axios filtering `plemena` endpoint)
- Vaccination date pickers
- "Free-text breed" field appears if "nenašli jste plemeno?" link is clicked
- Inline form validation with error messages under each field

---

### STAGE F6 — Registration Archive & Detail  *(Week 4)*

**Archive (`/prihlasky`):**
- Timeline list grouped by year
- Each entry: event name, horse name, date, total price badge, status badge (active/deleted)
- Quick PDF download icon per row

**Registration Detail (`/prihlasky/{id}`):**
- Two-column card layout
- Left: event info (name, dates, deadline with countdown if still open)
- Right: person + horse details, vaccination info
- Selected disciplines + stabling list with prices
- Total price in prominent display
- Action buttons: Edit (if deadline not passed), Delete (confirm modal), Print PDF

---

### STAGE F7 — CMT Membership Pages  *(Week 4–5)*

**My Memberships (`/clenstvi`):**
- Member cards with membership number badge (formatted e.g. #0002)
- Status banner: AKTIVNÍ (green) / NEAKTIVNÍ (amber)
- Annual fee and type displayed
- "Prodloužit na rok {year}" button

**New Membership Form (`/clenstvi/nova`):**
- Person dropdown with AJAX auto-fill
- Clean section cards: Member Details, Contact, Membership Type, Guardian (conditional)
- Membership type radio cards (visual cards showing year + price)

---

### STAGE F8 — Admin Panel  *(Week 5)*

**Dashboard (`/admin`):**
- Stats cards: total users, total horses, total events, total registrations
- Upcoming events list with registration counts
- Recent registrations feed

**Admin Event Detail:**
- Tab interface: Přihlášky | Startky | Ubytování | Nastavení
- Registrations tab: searchable/filterable table, start number assignment, row highlight for deleted
- Download buttons bar (XLS exports, bulk PDF)
- Start list tab: discipline-grouped accordion

---

## 7. Mock Data & Seeding

All seeders live in `database/seeders/`. Run with:

```bash
php artisan db:seed
# or for fresh install:
php artisan migrate:fresh --seed
```

---

### DatabaseSeeder.php (Master)

```php
public function run(): void
{
    $this->call([
        PlemenaSeeder::class,      // must be first (horse breeds lookup)
        UserSeeder::class,
        OsobaSeeder::class,
        KunSeeder::class,
        UdalostSeeder::class,
        PrihlaskaSeeder::class,
        ClenstviCmtSeeder::class,
    ]);
}
```

---

### PlemenaSeeder — All 50+ breeds

```php
$breeds = [
    ['kod' => 'ACHT',  'nazev' => 'Achaltekinský kůň'],
    ['kod' => 'AMHA',  'nazev' => 'Americký minihorse'],
    ['kod' => 'APH',   'nazev' => 'Americký Paint Horse'],
    ['kod' => 'A1/1',  'nazev' => 'Anglický plnokrevník'],
    ['kod' => 'APPA',  'nazev' => 'Appaloosa'],
    ['kod' => 'APPpo', 'nazev' => 'Appaloosa Pony'],
    ['kod' => 'OR1/1', 'nazev' => 'Arabský plnokrevník'],
    ['kod' => 'BP',    'nazev' => 'Barok Pinto'],
    ['kod' => 'b.PP',  'nazev' => 'Bez plemenné příslušnosti'],
    ['kod' => 'CMB',   'nazev' => 'Českomoravský belgik'],
    ['kod' => 'CNOR',  'nazev' => 'Český norik'],
    ['kod' => 'CSP',   'nazev' => 'Český sportovní pony'],
    ['kod' => 'CSPMK', 'nazev' => 'Český sportovní pony - malý kůň'],
    ['kod' => 'CT',    'nazev' => 'Český teplokrevník'],
    ['kod' => 'FAL',   'nazev' => 'Falabella'],
    ['kod' => 'FEELL', 'nazev' => 'Fell Pony'],
    ['kod' => 'FJ',    'nazev' => 'Fjordský kůň'],
    ['kod' => 'FK',    'nazev' => 'Fríský kůň'],
    ['kod' => 'HAFL',  'nazev' => 'Hafling'],
    ['kod' => 'HANN',  'nazev' => 'Hanoverský kůň'],
    ['kod' => 'KWPN',  'nazev' => 'Holandský teplokrevník'],
    ['kod' => 'HOLST', 'nazev' => 'Holštýnský kůň'],
    ['kod' => 'HUCUL', 'nazev' => 'Huculský kůň'],
    ['kod' => 'ICOB',  'nazev' => 'Irský Cob/Tinker'],
    ['kod' => 'KMSH',  'nazev' => 'Kentacky horský sedlový kůň'],
    ['kod' => 'KLUS',  'nazev' => 'Klusák'],
    ['kod' => 'KNAPS', 'nazev' => 'Knabstupper'],
    ['kod' => 'LIP',   'nazev' => 'Lipický kůň'],
    ['kod' => 'LUSIT', 'nazev' => 'Lusitano'],
    ['kod' => 'Mezek', 'nazev' => 'Mezek'],
    ['kod' => 'MiniA', 'nazev' => 'Miniappaloosa'],
    ['kod' => 'MMD',   'nazev' => 'Miniaturní středozemní osel'],
    ['kod' => 'MiniH', 'nazev' => 'MiniHorse'],
    ['kod' => 'MT',    'nazev' => 'Moravský teplokrevník'],
    ['kod' => 'Mula',  'nazev' => 'Mula'],
    ['kod' => 'NS',    'nazev' => 'Norik slezský'],
    ['kod' => 'Osel',  'nazev' => 'Osel'],
    ['kod' => 'CP',    'nazev' => 'Polský ušlechtilý polokrevník'],
    ['kod' => 'PRESP', 'nazev' => 'Pura Raza Española'],
    ['kod' => 'QH',    'nazev' => 'Quater Horse'],
    ['kod' => 'ShA',   'nazev' => 'Shagya Arab'],
    ['kod' => 'SHP',   'nazev' => 'Shetlandský pony'],
    ['kod' => 'SHPMH', 'nazev' => 'Shetlandský pony – sekce minihorse'],
    ['kod' => 'CS',    'nazev' => 'Slovenský teplokrevník česká PK'],
    ['kod' => 'ST',    'nazev' => 'Slovenský teplokrevník slovenská PK'],
    ['kod' => 'STKL',  'nazev' => 'Starokladrubský kůň'],
    ['kod' => 'TRAK',  'nazev' => 'Trakénský kůň'],
    ['kod' => 'Pony',  'nazev' => 'Typ pony'],
    ['kod' => 'WB',    'nazev' => 'Typ teplokrevný'],
    ['kod' => 'WCobD', 'nazev' => 'Welsh Cob sekce D'],
    ['kod' => 'WPB',   'nazev' => 'Welsh part bred'],
    ['kod' => 'WP',    'nazev' => 'Welsh pony'],
    ['kod' => 'WPC',   'nazev' => 'Welsh pony typu Cob sekce C'],
];
```

---

### UserSeeder — 5 mock users

```php
// Admin user
User::create([
    'jmeno' => 'Admin', 'prijmeni' => 'Správce',
    'email' => 'admin@koneakce.cz',
    'password' => bcrypt('admin123'),
    'is_admin' => true, 'gdpr_souhlas' => true,
]);

// Regular users
$users = [
    ['jmeno' => 'Pavla',    'prijmeni' => 'Cihlová',    'email' => 'pavla@example.cz',    'datum_narozeni' => '1961-05-30', 'pohlavi' => 'F', 'telefon' => '602012020'],
    ['jmeno' => 'Lucie',    'prijmeni' => 'Jerhótová',  'email' => 'lucie@example.cz',    'datum_narozeni' => '1985-03-15', 'pohlavi' => 'F', 'telefon' => '777234567'],
    ['jmeno' => 'Markéta',  'prijmeni' => 'Plekánková', 'email' => 'marketa@example.cz',  'datum_narozeni' => '1990-07-22', 'pohlavi' => 'F', 'telefon' => '603345678'],
    ['jmeno' => 'Michaels', 'prijmeni' => 'Kabenderle', 'email' => 'michael@example.cz',  'datum_narozeni' => '1978-11-08', 'pohlavi' => 'M', 'telefon' => '728456789'],
];
// Each with password: 'heslo123', gdpr_souhlas: true
```

---

### OsobaSeeder — 10 mock persons

```php
// Linked to users from UserSeeder
$osoby = [
    ['user_email' => 'pavla@example.cz',   'jmeno' => 'Pavla',    'prijmeni' => 'Cihlová',    'datum_narozeni' => '1961-05-30', 'staj' => 'Écola Equestrian'],
    ['user_email' => 'pavla@example.cz',   'jmeno' => 'Kristýna', 'prijmeni' => 'Kubů',       'datum_narozeni' => '2006-03-21', 'staj' => 'Stálek Úslné'],
    ['user_email' => 'lucie@example.cz',   'jmeno' => 'Lucie',    'prijmeni' => 'Jerhótová',  'datum_narozeni' => '2004-06-10', 'staj' => 'Écola Equestrian'],
    ['user_email' => 'lucie@example.cz',   'jmeno' => 'Melanie',  'prijmeni' => 'Kirchner',   'datum_narozeni' => '1983-11-20', 'staj' => 'Écola Equestrian'],
    ['user_email' => 'marketa@example.cz', 'jmeno' => 'Markéta',  'prijmeni' => 'Plekánková', 'datum_narozeni' => '1990-07-22', 'staj' => 'Academia BOJOU'],
    ['user_email' => 'marketa@example.cz', 'jmeno' => 'Gabriela', 'prijmeni' => 'Plekánková', 'datum_narozeni' => '2012-02-14', 'staj' => 'Academia BOJOU'],
    ['user_email' => 'michael@example.cz', 'jmeno' => 'Michael',  'prijmeni' => 'Kabenderle', 'datum_narozeni' => '1978-11-08', 'staj' => 'X-Trail Schachenhausen'],
    ['user_email' => 'michael@example.cz', 'jmeno' => 'Simon',    'prijmeni' => 'Meier',      'datum_narozeni' => '1982-04-05', 'staj' => 'X-Trail Schachenhausen'],
    ['user_email' => 'pavla@example.cz',   'jmeno' => 'Paulína',  'prijmeni' => 'Jindrčíová', 'datum_narozeni' => '1995-09-17', 'staj' => 'Stáj Úslné'],
    ['user_email' => 'lucie@example.cz',   'jmeno' => 'Alena',    'prijmeni' => 'Novotná',    'datum_narozeni' => '1975-06-03', 'staj' => 'Stáj Koníčkov'],
];
```

---

### KunSeeder — 12 mock horses

```php
$kone = [
    ['user_email' => 'pavla@example.cz',   'jmeno' => 'Chance',                   'plemeno_kod' => 'WPB',   'rok_narozeni' => 2012, 'pohlavi' => 'k', 'staj' => 'Écola Equestrian',    'cislo_prukazu' => '71047',    'cislo_hospodarstvi' => 'CZ31129487', 'ehv_datum' => '2021-09-12', 'aie_datum' => '2024-07-16', 'chripka_datum' => '2024-05-03'],
    ['user_email' => 'pavla@example.cz',   'jmeno' => 'Écola',                    'plemeno_kod' => 'A1/1',  'rok_narozeni' => 1997, 'pohlavi' => 'k', 'staj' => 'Écola Equestrian',    'cislo_prukazu' => '43092',    'cislo_hospodarstvi' => 'CZ31129487'],
    ['user_email' => 'pavla@example.cz',   'jmeno' => 'Escape Donna',             'plemeno_kod' => 'A1/1',  'rok_narozeni' => 2009, 'pohlavi' => 'k', 'staj' => 'Écola Equestrian',    'cislo_prukazu' => '58211'],
    ['user_email' => 'pavla@example.cz',   'jmeno' => 'Galf',                     'plemeno_kod' => 'WB',    'rok_narozeni' => 2019, 'pohlavi' => 'v', 'staj' => 'Écola Equestrian'],
    ['user_email' => 'pavla@example.cz',   'jmeno' => 'Hugo Lucky Star',          'plemeno_kod' => 'SHP',   'rok_narozeni' => 2011, 'pohlavi' => 'h', 'staj' => 'Stálek Úslné'],
    ['user_email' => 'pavla@example.cz',   'jmeno' => 'Merlik Of Kadov',          'plemeno_kod' => 'SHP',   'rok_narozeni' => 2011, 'pohlavi' => 'h', 'staj' => 'Stálek Úslné'],
    ['user_email' => 'pavla@example.cz',   'jmeno' => 'On Star',                  'plemeno_kod' => 'APPA',  'rok_narozeni' => 2006, 'pohlavi' => 'k', 'staj' => 'Écola Equestrian'],
    ['user_email' => 'marketa@example.cz', 'jmeno' => 'Accademia\'s Midnight Expresso','plemeno_kod' => 'APPA','rok_narozeni' => 2008,'pohlavi' => 'h', 'staj' => 'FARMA BOJOU'],
    ['user_email' => 'michael@example.cz', 'jmeno' => 'Atlantis Viking',          'plemeno_kod' => 'APPA',  'rok_narozeni' => 2014, 'pohlavi' => 'h', 'staj' => 'X-Trail Schachenhausen'],
    ['user_email' => 'michael@example.cz', 'jmeno' => 'Cuckoo Moonshine',         'plemeno_kod' => 'APPA',  'rok_narozeni' => 2015, 'pohlavi' => 'k', 'staj' => 'X-Trail Schachenhausen'],
    ['user_email' => 'lucie@example.cz',   'jmeno' => 'Joseph Small',             'plemeno_kod' => 'WPB',   'rok_narozeni' => 2017, 'pohlavi' => 'v', 'staj' => 'Grubac'],
    ['user_email' => 'pavla@example.cz',   'jmeno' => 'Star Queen Ann',           'plemeno_kod' => 'APPA',  'rok_narozeni' => 2022, 'pohlavi' => 'k', 'staj' => 'Écola Equestrian'],
];
```

---

### UdalostSeeder — 4 mock events

```php
$events = [
    [
        'nazev'  => 'OPEN závody Mountain Trail',
        'misto'  => 'Haklovy Dvory',
        'datum_zacatek' => '2026-04-04',
        'datum_konec'   => '2026-04-04',
        'uzavierka_prihlasek' => '2026-03-31',
        'kapacita' => null,
        'moznosti' => [
            ['nazev' => 'MT Ruka EASY 8+',          'cena' => 300, 'min_vek' => 8],
            ['nazev' => 'MT Ruka MEDIUM 8+',        'cena' => 300, 'min_vek' => 8],
            ['nazev' => 'MT Ruka SOLID 8+',         'cena' => 300, 'min_vek' => 8],
            ['nazev' => 'MT Sedo EASY 8+',          'cena' => 300, 'min_vek' => 8],
            ['nazev' => 'MT Sedo MEDIUM 8+',        'cena' => 300, 'min_vek' => 8],
            ['nazev' => 'MT Sedo SOLID 8+',         'cena' => 300, 'min_vek' => 8],
            ['nazev' => 'MT Ruka – vlastní volba 8+', 'cena' => 250, 'min_vek' => 8],
            ['nazev' => 'MT Sedo – vlastní volba 8+', 'cena' => 250, 'min_vek' => 8],
            ['nazev' => 'MT Tandem 12+',            'cena' => 300, 'min_vek' => 12],
            ['nazev' => 'Working Race ruka 6+ (sezónní speciál)', 'cena' => 300, 'min_vek' => 6],
            ['nazev' => 'Working Race sedo 6+ (sezónní speciál)', 'cena' => 300, 'min_vek' => 6],
            ['nazev' => 'Working Trail sedo 8+',    'cena' => 300, 'min_vek' => 8],
            ['nazev' => 'Trail s vodičem – děti do 8 let', 'cena' => 250, 'min_vek' => 0],
            ['nazev' => 'Administrativní poplatek', 'cena' => 100, 'je_administrativni_poplatek' => true],
        ],
        'ustajeni' => [
            ['nazev' => 'Vnitřní boxy 18m2',                                          'typ' => 'ustajeni', 'cena' => 500, 'kapacita' => 10],
            ['nazev' => 'Venkovní box 12m2',                                           'typ' => 'ustajeni', 'cena' => 300, 'kapacita' => 20],
            ['nazev' => 'Ohrada cca 20x25m (el.ohradník) - pro 2 koně z jedné stáje', 'typ' => 'ustajeni', 'cena' => 400, 'kapacita' => 8],
            ['nazev' => 'Padock cca 6x8m (el.ohradník)',                               'typ' => 'ustajeni', 'cena' => 250, 'kapacita' => 15],
            ['nazev' => 'Samostatný menší výběh mimo areál',                           'typ' => 'ustajeni', 'cena' => 0,   'kapacita' => null],
            ['nazev' => 'Trénink std.',                                                 'typ' => 'ostatni',  'cena' => 300, 'kapacita' => null],
        ],
    ],
    [
        'nazev'  => 'CMT Jaro 2026 – Plzeň',
        'misto'  => 'Ranč U Koníka, Plzeň',
        'datum_zacatek' => '2026-05-10',
        'datum_konec'   => '2026-05-11',
        'uzavierka_prihlasek' => '2026-04-30',
        'kapacita' => 60,
    ],
    [
        'nazev'  => 'Letní mistrovství CMT',
        'misto'  => 'Farma Borovice, Brno',
        'datum_zacatek' => '2026-07-18',
        'datum_konec'   => '2026-07-20',
        'uzavierka_prihlasek' => '2026-07-05',
        'kapacita' => 100,
    ],
    [
        'nazev'  => 'ARCHIV: OPEN závody Mountain Trail 2025',
        'misto'  => 'Haklovy Dvory',
        'datum_zacatek' => '2025-04-06',
        'datum_konec'   => '2025-04-06',
        'uzavierka_prihlasek' => '2025-03-28',
        'kapacita' => null,
        'aktivni' => false,
    ],
];
```

---

### PrihlaskaSeeder — ~15 registrations

```php
// Seed realistic registrations based on UserSeeder osoby + kone + udalosti
// Each registration:
// - Picks a valid person + horse from the same user
// - Selects 1-3 disciplines + admin fee
// - Optionally picks stabling
// - Assigns start numbers sequentially per event
// - Calculates and stores cena_celkem
```

---

### ClenstviCmtSeeder — 3 memberships

```php
$clenstvi = [
    ['osoba' => 'Pavla Cihlová',    'typ' => 'fyzicka_osoba', 'rok' => 2026, 'cena' => 500, 'aktivni' => true,  'evidencni_cislo' => '0002'],
    ['osoba' => 'Lucie Jerhótová',  'typ' => 'mladez',        'rok' => 2026, 'cena' => 200, 'aktivni' => true,  'evidencni_cislo' => '0047'],
    ['osoba' => 'Michael Kabenderle','typ' => 'fyzicka_osoba','rok' => 2025, 'cena' => 500, 'aktivni' => false, 'evidencni_cislo' => '0103'],
];
```

---

## 8. Admin Panel

The admin panel at `/admin` is accessible only to users with `is_admin = true`.

### Admin Routes Summary

```
GET  /admin                       → dashboard
     /admin/udalosti              → events CRUD
     /admin/udalosti/{id}         → event detail (tabs: registrations, starts, stabling)
     /admin/udalosti/{id}/prihlasky/...  → reports + exports
     /admin/users                 → user list, edit, flag admin
     /admin/clenstvi              → CMT membership management (activate, upload scan)
     /admin/start-cisla/{id}      → manage start number assignments
```

### Admin Features

- **Event management:** Full CRUD, attach discipline/stabling options, upload propozice PDF
- **Registration oversight:** View all registrations, start number assignment, view deleted
- **Exports:** XLS and PDF downloads (all report types)
- **User management:** View/edit users, grant admin, reset passwords
- **CMT membership admin:** Activate/deactivate, upload scanned forms, manual evidenční číslo assignment
- **Stats dashboard:** Registration counts, revenue by event, user growth

---

## 9. PDF, Excel & Email Engine

### PDF Generation (DomPDF)

```php
// app/Exports/PrihlaskaPdf.php
// Blade template: resources/views/pdf/prihlaska.blade.php
// Output: A4, portrait, styled with inline CSS (DomPDF limitation)

// Content mirrors registration detail view:
// - Event header, dates
// - Person info + age
// - Horse + vaccination table
// - Disciplines list + prices
// - Stabling choice
// - Total price
// - QR code (optional: link to online registration)
// - Footer: generated by koneakce.cz + date
```

### Email Notification

```php
// app/Mail/PrihlaskaPotvrzeniMail.php
// Triggered: after Prihlaska::store()
// Dispatched as queued job: SendPrihlaskaEmail::dispatch($prihlaska)
// Attaches: registration PDF
// Template: resources/views/emails/prihlaska-potvrzeni.blade.php
// Content: event summary, selected items, total price, link to view online
```

---

### Excel Exports — Implementation Method

> **IMPORTANT: The old site does NOT generate real binary Excel files.**
> All `.xls` exports are **HTML tables with inline CSS saved with a `.xls` extension**.
> Excel and LibreOffice open them transparently. The new Laravel site must replicate
> this exact pattern: render a Blade view as HTML and return it with
> `Content-Type: application/vnd.ms-excel` and a `.xls` filename.

**Laravel response pattern for all XLS exports:**

```php
// In ReportController — same pattern for every export:
public function exportSeznamPrihlasenych(Udalost $udalost)
{
    $prihlasky = $udalost->prihlasky()
        ->with(['osoba', 'kun', 'kunTandem', 'polozky'])
        ->where('smazana', false)
        ->orderBy('start_cislo')
        ->get();

    $html = view('exports.seznam-prihlasenych', compact('udalost', 'prihlasky'))->render();

    return response($html)
        ->header('Content-Type', 'application/vnd.ms-excel')
        ->header('Content-Disposition',
            'attachment; filename="Seznam_prihlasenych_' . $udalost->id . '.xls"')
        ->header('Pragma', 'no-cache')
        ->header('Expires', '0');
}
```

**Shared CSS for all export Blade templates** (`resources/views/exports/_styles.blade.php`):

```html
<style>
    td.reptd, td.reptdn, td.reptdc { border: 1px solid #cccccc; }
    td.reptds, td.reptdsn           { font-weight: bold; }
    td.reptdsum                     { background-color: #ffff60; }
    td.reptdh                       { background-color: #e0e0e0; color: black; }
    td.reptdh3                      { border: 1px solid #007070; background-color: #007070; color: white; }
    .reptdpozn                      { color: #909090; }
    .zvyr                           { font-weight: bold; color: #009fb0; }
    .zvyr2                          { color: #ff6000; }
    .smallfont                      { font-size: 8pt !important; }
    .kontakt_rucne                  { color: #00a000; }
    .reptr                          { background-color: white; }
    .reptr2                         { background-color: #f0f0f0; }
    .reptrold                       { background-color: #d0ffff; }
    .reptrsum                       { background-color: #ffff80; }
    .reptrsum2                      { background-color: #ffffb0; }
</style>
```

---

### Export 1 — `Seznam přihlášených` (Registered Participants List)

**File:** `Seznam_prihlasenych_{eventId}.xls`
**Blade:** `resources/views/exports/seznam-prihlasenych.blade.php`
**Triggered by:** `GET /admin/udalosti/{id}/prihlasky/export/seznam`

**HTML structure & exact columns:**

```html
<html><head><meta charset="utf-8">@include('exports._styles')</head><body>
<table cellspacing="5" cellpadding="5">
  <tr style="background-color:#549ee9;">
    <td style="text-align:center;">st.č.</td>
    <td style="width:180px;">Jméno osoba</td>
    <td>Jméno a pohlaví koně + kůň tandem ruka</td>
    <td>Stáj</td>
    <td>Datum narození</td>
    <td>Cena celkem</td>
  </tr>
  @foreach($prihlasky as $p)
  {{-- Deleted registrations: background-color:#ffcccc --}}
  <tr @if($p->smazana) style="background-color:#ffcccc;" @endif>
    <td style="text-align:center;">{{ $p->start_cislo }}</td>
    {{-- Person name with age category suffix: (m*) <8yrs, (m) youth, (j) junior --}}
    <td>{{ $p->osoba->prijmeni }} {{ $p->osoba->jmeno }}{{ $p->vekKategorie() }}</td>
    {{-- Horse: "Name (BREED_KOD, year, sex)" + tandem if present --}}
    <td>{{ $p->kun->jmeno }} ({{ $p->kun->plemeno_kod }}, {{ $p->kun->rok_narozeni }}, {{ $p->kun->pohlavi }})
        @if($p->kunTandem), {{ $p->kunTandem->jmeno }} ({{ $p->kunTandem->plemeno_kod }}, {{ $p->kunTandem->rok_narozeni }}, {{ $p->kunTandem->pohlavi }})@endif
    </td>
    <td>{{ $p->osoba->staj }}</td>
    {{-- Date format: DD.MM.YYYY with age in small span --}}
    <td>{{ $p->osoba->datum_narozeni->format('d.m.Y') }}
        <span style="font-size:10px;">({{ $p->osoba->datum_narozeni->age }} let)</span>
    </td>
    <td style="text-align:right;">{{ $p->cena_celkem }}</td>
  </tr>
  @endforeach
</table>
</body></html>
```

**Business rules:**
- Rows alternating white / `#f0f0f0` (`.reptr` / `.reptr2`)
- Deleted registrations: row background `#ffcccc`
- Age category appended to person name: ` (m*)` if age < 8, ` (m)` if age < 18, ` (j)` if 14–17 (per CMT rules)
- Tandem registrations (second horse) shown in same horse column, comma-separated
- `?s=1` query param: show deleted registrations only (same export, filtered)

---

### Export 2 — `Přihlášení a jejich disciplíny` (Participants & Disciplines Matrix)

**File:** `Prihlaseneni_a_jejich_discipliny_{eventId}.xls`
**Blade:** `resources/views/exports/discipliny.blade.php`
**Triggered by:** `GET /admin/udalosti/{id}/prihlasky/export/discipliny`

**HTML structure — dynamic column matrix:**

```html
<table border="1">
  <tr>
    <td>č.</td>
    <td>Jméno</td>
    <td>Kůň</td>
    <td>pozn.</td>
    {{-- One column per discipline: header contains name + price in sub-table --}}
    @foreach($moznosti as $moz)
    <td>
      <table><tr><td>{{ $moz->nazev }}</td></tr>
             <tr><td>{{ number_format($moz->cena, 2) }}</td></tr>
      </table>
    </td>
    @endforeach
    {{-- One column per stabling option: "Name (price Kč)" --}}
    @foreach($ustajeniOptions as $u)
    <td>{{ $u->nazev }} ({{ (int)$u->cena }}&nbsp;Kč)</td>
    @endforeach
  </tr>
  @foreach($prihlasky as $p)
  <tr>
    <td>{{ $p->start_cislo }}</td>
    <td>{{ $p->osoba->prijmeni }} {{ $p->osoba->jmeno }}</td>
    <td>{{ $p->kun->jmeno }}</td>
    <td>{{ $p->poznamka }}</td>
    {{-- Tick mark (X or checkmark) if discipline selected --}}
    @foreach($moznosti as $moz)
    <td style="text-align:center;">
      {{ $p->polozky->contains('moznost_id', $moz->id) ? 'X' : '' }}
    </td>
    @endforeach
    @foreach($ustajeniOptions as $u)
    <td style="text-align:center;">
      {{ $p->ustajeniChoices->contains('ustajeni_id', $u->id) ? 'X' : '' }}
    </td>
    @endforeach
  </tr>
  @endforeach
</table>
```

---

### Export 3 — `Emaily přihlášených` (Email List)

**File:** `Emaily_prihlasenych_{eventId}.xls`
**Blade:** `resources/views/exports/emaily.blade.php`
**Triggered by:** `GET /admin/udalosti/{id}/prihlasky/export/emaily`

**Exact columns (from real file analysis):**

```html
<table>
  <tr><td>ID Přihlášky</td><td>Email</td><td>Příjmení Jméno</td></tr>
  @foreach($prihlasky as $p)
  <tr>
    <td>{{ $p->id }}</td>
    <td>{{ $p->user->email }}</td>
    {{-- Person name who is registered (not the account holder) --}}
    <td>{{ $p->osoba->prijmeni }} {{ $p->osoba->jmeno }}</td>
  </tr>
  @endforeach
</table>
```

**Note:** Rows are sorted by email (grouped so one account's multiple registrations appear together).

---

### Export 4 — `Seznam koní pro veterinární přejímku` (Horses for Vet Check)

**File:** `Kone_vet_prejimka_{eventId}.xls`
**Blade:** `resources/views/exports/vet-prejimka.blade.php`
**Triggered by:** `GET /admin/udalosti/{id}/prihlasky/export/kone`

**Exact structure (from real file analysis):**

```html
<h1>{{ $udalost->nazev }}</h1>
<h3>{{ $udalost->datum_zacatek->format('d.m.Y') }}  - {{ $udalost->misto }}</h3>
<table border="1" cellspacing="10" cellpadding="10">
  <tr>
    <td>Jméno koně</td>
    <td>Majitel koně</td>
    <td>číslo průkazu koně / číslo hosp.</td>
    <td>Pozn.</td>
  </tr>
  @foreach($kone as $k)
  <tr>
    <td>{{ $k->jmeno }}</td>
    {{-- Owner info: name + full address (from horse's majitel_jmeno_adresa field) --}}
    <td>{{ $k->majitel_jmeno_adresa }}</td>
    {{-- Passport number / farm number in one cell, separated --}}
    <td>{{ $k->cislo_prukazu }}</td>
    <td>{{ $k->cislo_hospodarstvi }}</td>
    <td>&nbsp;</td>{{-- Notes column — blank --}}
  </tr>
  @endforeach
</table>
```

**Note:** Only unique horses (de-duplicated by horse ID — a horse with multiple starts appears once).
Sorted alphabetically by horse name.

---

### Export 5 — `Seznam startek` (Start List by Discipline)

**File:** `Seznam_startek_{eventId}.xls`
**Blade:** `resources/views/exports/seznam-startek.blade.php`
**Triggered by:** `GET /admin/udalosti/{id}/startky/export`

**Exact structure (from real file analysis):**

```html
{{-- One H2 + table block per discipline --}}
@foreach($moznostiSeDisciplinami as $moz)
<h2> {{ $moz->nazev }}</h2>
<table cellspacing="0" cellpadding="5" border="1" width="704">
  <tr>
    <td style="width:42px;text-align:center;">st.č.</td>
    <td style="width:170px;">jméno</td>
    <td style="width:300px;">kůň</td>
    <td style="width:150px;">stáj</td>
  </tr>
  @foreach($moz->registrations as $p)
  <tr>
    <td style="text-align:center;">{{ $p->start_cislo }}</td>
    {{-- Person name + age category suffix --}}
    <td>{{ $p->osoba->prijmeni }} {{ $p->osoba->jmeno }}{{ $p->vekKategorie() }}</td>
    {{-- Horse: "Name (BREED_KOD, year, sex)" --}}
    <td>{{ $p->kun->jmeno }} ({{ $p->kun->plemeno_kod }}, {{ $p->kun->rok_narozeni }}, {{ $p->kun->pohlavi }})</td>
    <td>{{ $p->osoba->staj }}</td>
  </tr>
  @endforeach
  <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
</table>
@endforeach
```

**Tandem disciplines:** Show two rows or a second horse column:
```html
{{-- For tandem: kůň column shows: "Horse1 (breed,yr,sex) + Horse2 (breed,yr,sex)" --}}
<td>{{ $p->kun->jmeno }} ({{ $p->kun->plemeno_kod }}, ...)
  @if($p->kunTandem) + {{ $p->kunTandem->jmeno }} (...)@endif
</td>
```

---

### Export 6 — `Seznam disciplín a počet startů` (Discipline Start Counts)

**File:** `Seznam_disciplin_a_pocet_startu_{eventId}.xls`
**Blade:** `resources/views/exports/discipliny-pocty.blade.php`
**Triggered by:** `GET /admin/udalosti/{id}/startky/export/discipliny`

**Exact structure (from real file analysis — simplest export):**

```html
<h2>{{ $udalost->nazev }}</h2>
<table cellspacing="0" cellpadding="5" border="1" width="500">
  @foreach($pocty as $nazev => $count)
  <tr>
    <td> {{ $nazev }}</td>
    <td> {{ $count }}</td>
  </tr>
  @endforeach
</table>
<h2>Celkem startů: {{ $totalStartu }}</h2>
```

---

### Export 7 — `Ustájení ubytování strava` (Stabling/Accommodation/Catering)

**File:** `Ustajeni_ubytovani_strava_{eventId}.xls`
**Blade:** `resources/views/exports/ustajeni.blade.php`
**Triggered by:** `GET /admin/udalosti/{id}/ubytovani/export`

**Exact structure (from real file analysis):**

```html
{{-- Each category (ustajeni, ubytovani, ostatni, strava) gets a red H1 --}}
<h1 style="color:red;">Ustajeni</h1>
@foreach($ustajeniByTyp['ustajeni'] as $option)
<h2> {{ $option->nazev }}</h2>
<table cellspacing="0" cellpadding="5" border="1" width="704">
  <tr>
    <td style="width:42px;text-align:center;">st.č.</td>
    <td style="width:170px;">jméno</td>
    <td style="width:300px;">kůň</td>   {{-- horse name + sex ONLY (no breed/year) --}}
    <td style="width:150px;">stáj</td>
  </tr>
  @foreach($option->registrations as $p)
  <tr>
    <td style="text-align:center;">{{ $p->start_cislo }}</td>
    <td>{{ $p->osoba->prijmeni }} {{ $p->osoba->jmeno }}</td>
    <td>{{ $p->kun->jmeno }} ({{ $p->kun->pohlavi }})</td>  {{-- Only sex, NOT breed+year --}}
    <td>{{ $p->osoba->staj }}</td>
  </tr>
  @endforeach
</table>
@endforeach

<h1 style="color:red;">Ubytovani</h1>
{{-- same structure for typ='ubytovani' --}}

<h1 style="color:red;">Ostatní (trénink std.)</h1>
{{-- same structure for typ='ostatni' --}}

<h1 style="color:red;">Strava</h1>
{{-- same structure for typ='strava' --}}
```

---

### Export Controller — Complete Route Map

```php
// routes/web.php — admin export group
Route::prefix('admin/udalosti/{udalost}')->middleware(['auth','admin'])->group(function () {
    // Registration list + variants
    Route::get('prihlasky/export/seznam',     [ReportController::class, 'exportSeznam']);
    Route::get('prihlasky/export/discipliny', [ReportController::class, 'exportDiscipliny']);
    Route::get('prihlasky/export/emaily',     [ReportController::class, 'exportEmaily']);
    Route::get('prihlasky/export/kone',       [ReportController::class, 'exportKone']);
    Route::get('prihlasky/export/bulk-pdf',   [ReportController::class, 'exportBulkPdf']);
    Route::get('prihlasky/smazane',           [ReportController::class, 'smazane']);

    // Start lists
    Route::get('startky',                     [ReportController::class, 'startky']);
    Route::get('startky/export',              [ReportController::class, 'exportStartky']);
    Route::get('startky/export/discipliny',   [ReportController::class, 'exportDisciplinyPocty']);

    // Stabling
    Route::get('ubytovani',                   [ReportController::class, 'ubytovani']);
    Route::get('ubytovani/export',            [ReportController::class, 'exportUstajeni']);
});
```

### Export Blade Template Files

```
resources/views/exports/
  _styles.blade.php              ← shared CSS include
  seznam-prihlasenych.blade.php  ← Export 1
  discipliny.blade.php           ← Export 2
  emaily.blade.php               ← Export 3
  vet-prejimka.blade.php         ← Export 4
  seznam-startek.blade.php       ← Export 5
  discipliny-pocty.blade.php     ← Export 6
  ustajeni.blade.php             ← Export 7
```

### Helper Method on `Prihlaska` Model

```php
// app/Models/Prihlaska.php

/**
 * Returns age category suffix string used in exports.
 * m* = child led by handler (under 8 yrs), m = youth (under 18), j = junior (14-17)
 */
public function vekKategorie(): string
{
    $vek = $this->osoba->datum_narozeni->age;
    if ($vek < 8)  return ' (m*)';
    if ($vek < 14) return ' (m)';
    if ($vek < 18) return ' (j)';
    return '';
}
```

> **Note on `maatwebsite/excel`:** This package is NOT needed for these exports.
> The HTML-as-XLS method is simpler and exactly replicates the old site's output.
> `maatwebsite/excel` may still be useful if real `.xlsx` format is desired
> for a future enhancement, but for faithful replication, use Blade + response headers.

---

## 10. Security & GDPR

### Security Measures

- [ ] All forms: Laravel CSRF tokens
- [ ] All inputs: validated via `FormRequest` classes (no raw `$request->all()` stored)
- [ ] SQL injection: Eloquent ORM only (no raw queries unless parameterised)
- [ ] XSS: Blade `{{ }}` auto-escaping on all outputs; `{!! !!}` only for trusted admin HTML
- [ ] File uploads: validated mime type + extension, stored outside webroot in `storage/`
- [ ] Authentication: `bcrypt` password hashing, rate-limited login (5 attempts / minute)
- [ ] Session: HTTP-only cookies, regenerate session ID on login
- [ ] HTTPS: enforce via Apache `Redirect` or `.htaccess` + HSTS header
- [ ] Admin routes: `AdminMiddleware` + route grouping

### GDPR Compliance

- [ ] Consent recorded with timestamp on: user registration, person creation, registration form, CMT membership
- [ ] Revocation: `gdpr_odvolano` flag + timestamp on persons; admin notified
- [ ] Data minimisation: no unnecessary PII collected
- [ ] Right to erasure: `SoftDeletes` on all user-related models; admin "purge user" action for hard delete
- [ ] Data export: admin can export all data for a user (CSV) — `GDPR\UserDataExport`
- [ ] Privacy policy page at `/gdpr`

---

## 11. Deployment Checklist

```bash
# On Ubuntu server, after upload:

cd /var/www/koneakce

# Install production dependencies
composer install --no-dev --optimize-autoloader

# Build frontend assets
npm ci && npm run build

# Run migrations
php artisan migrate --force

# Seed breeds + admin user (first deploy only)
php artisan db:seed --class=PlemenaSeeder
php artisan db:seed --class=UserSeeder

# Cache for performance
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Storage link (for public uploads)
php artisan storage:link

# Set permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# Start queue worker (via Supervisor)
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start koneakce-worker:*

# Set up cron
crontab -e
# Add: * * * * * cd /var/www/koneakce && php artisan schedule:run >> /dev/null 2>&1
```

### SSL Certificate (Let's Encrypt)

```bash
sudo apt install certbot python3-certbot-apache -y
sudo certbot --apache -d koneakce.cz -d www.koneakce.cz
sudo systemctl reload apache2
```

---

## 12. Project File Structure

```
koneakce/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/                        ← Breeze auth controllers
│   │   │   ├── Admin/
│   │   │   │   ├── UdalostController.php
│   │   │   │   ├── ReportController.php
│   │   │   │   ├── UserController.php
│   │   │   │   └── ClenstviAdminController.php
│   │   │   ├── UdalostController.php
│   │   │   ├── OsobaController.php
│   │   │   ├── KunController.php
│   │   │   ├── PrihlaskaController.php
│   │   │   ├── ClenstviController.php
│   │   │   └── AjaxController.php           ← all AJAX endpoints
│   │   ├── Middleware/
│   │   │   └── AdminMiddleware.php
│   │   └── Requests/                        ← FormRequest validation classes
│   │       ├── StorePrihlaskaRequest.php
│   │       ├── StoreKunRequest.php
│   │       ├── StoreOsobaRequest.php
│   │       └── ...
│   ├── Models/
│   │   ├── User.php
│   │   ├── Osoba.php
│   │   ├── Kun.php
│   │   ├── Udalost.php
│   │   ├── UdalostMoznost.php
│   │   ├── UdalostUstajeni.php
│   │   ├── Prihlaska.php
│   │   ├── PrihlaskaPolozka.php
│   │   ├── PrihlaskaUstajeni.php
│   │   ├── ClenstviCmt.php
│   │   └── Pleme.php
│   ├── Services/
│   │   └── PrihlaskaService.php             ← business logic (admin fee, price calc)
│   ├── Mail/
│   │   └── PrihlaskaPotvrzeniMail.php
│   ├── Jobs/
│   │   └── SendPrihlaskaEmail.php
│   ├── Exports/                             ← Maatwebsite Excel exports
│   │   ├── SeznamPrihlasenych.php
│   │   ├── DisciplinyExport.php
│   │   ├── EmailyExport.php
│   │   ├── VetPrejimkaExport.php
│   │   ├── UstajeniExport.php
│   │   └── StartkyExport.php
│   └── Policies/
│       ├── OsobaPolicy.php
│       ├── KunPolicy.php
│       └── PrihlaskaPolicy.php
│
├── database/
│   ├── migrations/                          ← all migrations (see Stage B1–B6)
│   └── seeders/
│       ├── DatabaseSeeder.php
│       ├── PlemenaSeeder.php
│       ├── UserSeeder.php
│       ├── OsobaSeeder.php
│       ├── KunSeeder.php
│       ├── UdalostSeeder.php
│       ├── PrihlaskaSeeder.php
│       └── ClenstviCmtSeeder.php
│
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   │   ├── app.blade.php
│   │   │   ├── guest.blade.php
│   │   │   └── admin.blade.php
│   │   ├── components/
│   │   ├── auth/
│   │   ├── udalosti/
│   │   │   ├── index.blade.php
│   │   │   └── show.blade.php
│   │   ├── prihlasky/
│   │   │   ├── create.blade.php            ← multi-step wizard
│   │   │   ├── edit.blade.php
│   │   │   └── show.blade.php
│   │   ├── osoby/
│   │   ├── kone/
│   │   ├── clenstvi/
│   │   ├── admin/
│   │   ├── pdf/
│   │   │   └── prihlaska.blade.php         ← PDF template
│   │   └── emails/
│   │       └── prihlaska-potvrzeni.blade.php
│   ├── css/
│   │   └── app.css                         ← Tailwind imports + custom theme
│   └── js/
│       └── app.js                          ← Alpine.js init + Axios setup
│
├── routes/
│   ├── web.php                             ← all web routes
│   └── api.php                             ← (not used; AJAX via web routes)
│
├── public/
│   └── (compiled assets via Vite)
│
└── storage/
    └── app/public/
        ├── propozice/                      ← event regulations PDFs
        └── clenstvi/                       ← scanned membership forms
```

---

## Build Timeline Summary

| Week | Backend Stages | Frontend Stages |
|---|---|---|
| 1 | B1 (Auth), B2 (Persons) | F1 (Layout/Nav), F2 (Auth pages) |
| 2 | B3 (Horses), B4 (Events) | F3 (Events), F5 (Persons/Horses) |
| 3 | B5 start (Registrations) | F4 (Registration wizard) |
| 4 | B5 finish, B6 (CMT) | F6 (Archive/Detail), F7 (CMT) |
| 5 | B7 (Reports), B8 (Queue) | F8 (Admin panel) |
| 6 | Testing, seeding, hardening | Mobile polish, cross-browser QA |
| 7 | Deployment to Ubuntu/Apache | UAT with client, go-live |

---

*Plan version 1.0 — Generated 2026-04-04*
*Stack: PHP 8.2 · Laravel 11 · MySQL 8 · Apache 2.4 · Ubuntu 22.04*
*Source analysis: koneakce.cz legacy site (flat PHP, jQuery, HTML tables)*
