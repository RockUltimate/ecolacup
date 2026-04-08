# Event Edit Page Refactor - Implementation Plan

> **For agentic workers:** Use superpowers:subagent-driven-development to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Restructure the admin event edit page into a tab-based interface with separate sections for description, disciplines, and services, adding edit capabilities and file upload support for disciplines.

**Architecture:** 
- Database: Add columns to UdalostMoznost and UdalostUstajeni for storing description and file paths
- Routes: Add PUT routes for updating disciplines and services
- Controller: Add methods to edit/update disciplines and services  
- Views: Replace single-page form with tab-based layout (Popis | Disciplíny | Služby | Přihlášky | Startky)

**Tech Stack:** Laravel 11, Blade templates, Alpine.js for interactivity, file uploads to public storage

---

## File Structure

**Database:**
- `database/migrations/2026_04_08_000000_add_files_and_description_to_disciplines_and_services.php` (new)

**Models:**
- `app/Models/UdalostMoznost.php` (modify)
- `app/Models/UdalostUstajeni.php` (modify)

**Controllers:**
- `app/Http/Controllers/Admin/UdalostController.php` (modify)

**Routes:**
- `routes/web.php` (modify)

**Views:**
- `resources/views/admin/udalosti/_tabs.blade.php` (modify)
- `resources/views/admin/udalosti/edit.blade.php` (replace)
- `resources/views/admin/udalosti/_popis.blade.php` (new - moved from _form.blade.php)
- `resources/views/admin/udalosti/_discipliny.blade.php` (new - disciplines management)
- `resources/views/admin/udalosti/_sluzby.blade.php` (new - services management)
- `resources/views/admin/udalosti/_form.blade.php` (delete - content moved to _popis.blade.php)

---

## Tasks

### Task 1: Create Migration for File/Description Columns

**Files:**
- Create: `database/migrations/2026_04_08_000000_add_files_and_description_to_disciplines_and_services.php`

- [ ] **Step 1: Write the migration file**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('udalost_moznosti', function (Blueprint $table) {
            $table->text('popis_text')->nullable()->after('je_administrativni_poplatek');
            $table->text('popis_html')->nullable()->after('popis_text');
            $table->string('foto_path')->nullable()->after('popis_html');
            $table->string('pdf_path')->nullable()->after('foto_path');
        });

        Schema::table('udalost_ustajeni', function (Blueprint $table) {
            $table->text('popis_text')->nullable()->after('kapacita');
            $table->text('popis_html')->nullable()->after('popis_text');
            $table->string('foto_path')->nullable()->after('popis_html');
            $table->string('pdf_path')->nullable()->after('foto_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('udalost_moznosti', function (Blueprint $table) {
            $table->dropColumn(['popis_text', 'popis_html', 'foto_path', 'pdf_path']);
        });

        Schema::table('udalost_ustajeni', function (Blueprint $table) {
            $table->dropColumn(['popis_text', 'popis_html', 'foto_path', 'pdf_path']);
        });
    }
};
```

- [ ] **Step 2: Run migration**

Run: `php artisan migrate`
Expected: Migration runs successfully with no errors

- [ ] **Step 3: Commit**

```bash
git add database/migrations/2026_04_08_000000_add_files_and_description_to_disciplines_and_services.php
git commit -m "feat: add file and description columns to disciplines and services"
```

---

### Task 2: Update UdalostMoznost Model

**Files:**
- Modify: `app/Models/UdalostMoznost.php`

- [ ] **Step 1: Update fillable array and add casts**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UdalostMoznost extends Model
{
    /**
     * @var string
     */
    protected $table = 'udalost_moznosti';
    /**
     * @var list<string>
     */
    protected $fillable = [
        'udalost_id',
        'nazev',
        'min_vek',
        'cena',
        'poradi',
        'je_administrativni_poplatek',
        'popis_text',
        'popis_html',
        'foto_path',
        'pdf_path',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'min_vek' => 'integer',
            'cena' => 'decimal:2',
            'poradi' => 'integer',
            'je_administrativni_poplatek' => 'boolean',
        ];
    }

    public function udalost(): BelongsTo
    {
        return $this->belongsTo(Udalost::class);
    }

    public function prihlaskyPolozky(): HasMany
    {
        return $this->hasMany(PrihlaskaPolozka::class, 'moznost_id');
    }
}
```

- [ ] **Step 2: Commit**

```bash
git add app/Models/UdalostMoznost.php
git commit -m "feat: add file and description fields to UdalostMoznost model"
```

---

### Task 3: Update UdalostUstajeni Model

**Files:**
- Modify: `app/Models/UdalostUstajeni.php`

- [ ] **Step 1: Read the file**

No code provided yet - read current file to understand structure

- [ ] **Step 2: Add fields to fillable array**

Update the `$fillable` array to include:
- 'popis_text'
- 'popis_html'
- 'foto_path'
- 'pdf_path'

- [ ] **Step 3: Commit**

```bash
git add app/Models/UdalostUstajeni.php
git commit -m "feat: add file and description fields to UdalostUstajeni model"
```

---

### Task 4: Update Routes for Discipline Editing

**Files:**
- Modify: `routes/web.php` lines 73-74

- [ ] **Step 1: Add edit and update routes for disciplines**

Replace:
```php
Route::post('/udalosti/{udalost}/moznosti', [AdminUdalostController::class, 'storeMoznost'])->name('udalosti.moznosti.store');
Route::delete('/udalosti/{udalost}/moznosti/{moznost}', [AdminUdalostController::class, 'destroyMoznost'])->name('udalosti.moznosti.destroy');
```

With:
```php
Route::post('/udalosti/{udalost}/moznosti', [AdminUdalostController::class, 'storeMoznost'])->name('udalosti.moznosti.store');
Route::get('/udalosti/{udalost}/moznosti/{moznost}/edit', [AdminUdalostController::class, 'editMoznost'])->name('udalosti.moznosti.edit');
Route::put('/udalosti/{udalost}/moznosti/{moznost}', [AdminUdalostController::class, 'updateMoznost'])->name('udalosti.moznosti.update');
Route::delete('/udalosti/{udalost}/moznosti/{moznost}', [AdminUdalostController::class, 'destroyMoznost'])->name('udalosti.moznosti.destroy');
```

- [ ] **Step 2: Add edit and update routes for services**

Replace:
```php
Route::post('/udalosti/{udalost}/ustajeni', [AdminUdalostController::class, 'storeUstajeni'])->name('udalosti.ustajeni.store');
Route::delete('/udalosti/{udalost}/ustajeni/{ustajeni}', [AdminUdalostController::class, 'destroyUstajeni'])->name('udalosti.ustajeni.destroy');
```

With:
```php
Route::post('/udalosti/{udalost}/ustajeni', [AdminUdalostController::class, 'storeUstajeni'])->name('udalosti.ustajeni.store');
Route::get('/udalosti/{udalost}/ustajeni/{ustajeni}/edit', [AdminUdalostController::class, 'editUstajeni'])->name('udalosti.ustajeni.edit');
Route::put('/udalosti/{udalost}/ustajeni/{ustajeni}', [AdminUdalostController::class, 'updateUstajeni'])->name('udalosti.ustajeni.update');
Route::delete('/udalosti/{udalost}/ustajeni/{ustajeni}', [AdminUdalostController::class, 'destroyUstajeni'])->name('udalosti.ustajeni.destroy');
```

- [ ] **Step 3: Commit**

```bash
git add routes/web.php
git commit -m "feat: add edit and update routes for disciplines and services"
```

---

### Task 5: Update UdalostController with Edit/Update Methods for Disciplines

**Files:**
- Modify: `app/Http/Controllers/Admin/UdalostController.php` (add methods after `storeMoznost`)

- [ ] **Step 1: Add `editMoznost` method**

Add after the `storeMoznost` method:

```php
public function editMoznost(Udalost $udalost, UdalostMoznost $moznost): View
{
    if ($moznost->udalost_id !== $udalost->id) {
        abort(404);
    }

    return view('admin.udalosti._discipliny_edit_modal', [
        'udalost' => $udalost,
        'moznost' => $moznost,
    ]);
}
```

- [ ] **Step 2: Add `updateMoznost` method**

Add after `editMoznost`:

```php
public function updateMoznost(Udalost $udalost, UdalostMoznost $moznost, StoreAdminUdalostMoznostRequest $request): RedirectResponse
{
    if ($moznost->udalost_id !== $udalost->id) {
        abort(404);
    }

    $validated = $request->validated();
    $validated['poradi'] = $validated['poradi'] ?? 0;
    $validated['je_administrativni_poplatek'] = $request->boolean('je_administrativni_poplatek', false);

    // Handle file uploads
    if ($request->hasFile('foto_path')) {
        $storedPath = $request->file('foto_path')->store('disciplines', 'public');
        if ($moznost->foto_path && $moznost->foto_path !== $storedPath) {
            Storage::disk('public')->delete($moznost->foto_path);
        }
        $validated['foto_path'] = $storedPath;
    }
    if ($request->hasFile('pdf_path')) {
        $storedPath = $request->file('pdf_path')->store('disciplines', 'public');
        if ($moznost->pdf_path && $moznost->pdf_path !== $storedPath) {
            Storage::disk('public')->delete($moznost->pdf_path);
        }
        $validated['pdf_path'] = $storedPath;
    }

    $moznost->update($validated);

    return redirect()->route('admin.udalosti.edit', $udalost)->with('status', 'moznost-updated');
}
```

- [ ] **Step 3: Commit**

```bash
git add app/Http/Controllers/Admin/UdalostController.php
git commit -m "feat: add editMoznost and updateMoznost methods"
```

---

### Task 6: Update UdalostController with Edit/Update Methods for Services

**Files:**
- Modify: `app/Http/Controllers/Admin/UdalostController.php` (add methods after `storeUstajeni`)

- [ ] **Step 1: Add `editUstajeni` method**

Add after the `storeUstajeni` method:

```php
public function editUstajeni(Udalost $udalost, UdalostUstajeni $ustajeni): View
{
    if ($ustajeni->udalost_id !== $udalost->id) {
        abort(404);
    }

    return view('admin.udalosti._sluzby_edit_modal', [
        'udalost' => $udalost,
        'ustajeni' => $ustajeni,
    ]);
}
```

- [ ] **Step 2: Add `updateUstajeni` method**

Add after `editUstajeni`:

```php
public function updateUstajeni(Udalost $udalost, UdalostUstajeni $ustajeni, StoreAdminUdalostUstajeniRequest $request): RedirectResponse
{
    if ($ustajeni->udalost_id !== $udalost->id) {
        abort(404);
    }

    $validated = $request->validated();

    // Handle file uploads
    if ($request->hasFile('foto_path')) {
        $storedPath = $request->file('foto_path')->store('services', 'public');
        if ($ustajeni->foto_path && $ustajeni->foto_path !== $storedPath) {
            Storage::disk('public')->delete($ustajeni->foto_path);
        }
        $validated['foto_path'] = $storedPath;
    }
    if ($request->hasFile('pdf_path')) {
        $storedPath = $request->file('pdf_path')->store('services', 'public');
        if ($ustajeni->pdf_path && $ustajeni->pdf_path !== $storedPath) {
            Storage::disk('public')->delete($ustajeni->pdf_path);
        }
        $validated['pdf_path'] = $storedPath;
    }

    $ustajeni->update($validated);

    return redirect()->route('admin.udalosti.edit', $udalost)->with('status', 'ustajeni-updated');
}
```

- [ ] **Step 3: Commit**

```bash
git add app/Http/Controllers/Admin/UdalostController.php
git commit -m "feat: add editUstajeni and updateUstajeni methods"
```

---

### Task 7: Update _tabs Component with New Tab Structure

**Files:**
- Modify: `resources/views/admin/udalosti/_tabs.blade.php`

- [ ] **Step 1: Replace tab definitions**

Replace the entire `$tabs` array:

```php
@php
    $tabs = [
        'popis' => ['label' => 'Popis', 'href' => route('admin.udalosti.edit', $udalost) . '#popis'],
        'discipliny' => ['label' => 'Disciplíny', 'href' => route('admin.udalosti.edit', $udalost) . '#discipliny'],
        'sluzby' => ['label' => 'Služby', 'href' => route('admin.udalosti.edit', $udalost) . '#sluzby'],
        'prihlasky' => ['label' => 'Přihlášky', 'href' => route('admin.reports.prihlasky', $udalost)],
        'startky' => ['label' => 'Startky', 'href' => route('admin.reports.startky', $udalost)],
    ];
@endphp
```

Note: Remove the 'overview' and 'ubytovani' tabs, change 'settings' to 'popis'

- [ ] **Step 2: Commit**

```bash
git add resources/views/admin/udalosti/_tabs.blade.php
git commit -m "feat: update tabs to new structure (Popis, Disciplíny, Služby, Přihlášky, Startky)"
```

---

### Task 8: Create _popis.blade.php (Event Description Form)

**Files:**
- Create: `resources/views/admin/udalosti/_popis.blade.php`

- [ ] **Step 1: Create the component**

Content is moved from `_form.blade.php`:

```blade
<section class="panel p-6 sm:p-8">
    <form method="POST" action="{{ route('admin.udalosti.update', $udalost) }}" class="space-y-6" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div>
            <x-input-label for="nazev" :value="'Název'" />
            <x-text-input id="nazev" name="nazev" type="text" class="mt-1 block w-full" :value="old('nazev', $udalost->nazev ?? '')" required />
            <x-input-error :messages="$errors->get('nazev')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="misto" :value="'Místo'" />
            <x-text-input id="misto" name="misto" type="text" class="mt-1 block w-full" :value="old('misto', $udalost->misto ?? '')" required />
            <x-input-error :messages="$errors->get('misto')" class="mt-2" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <x-input-label for="datum_zacatek" :value="'Datum začátku'" />
                <x-text-input id="datum_zacatek" name="datum_zacatek" type="text" class="mt-1 block w-full" :value="old('datum_zacatek', isset($udalost) && $udalost->datum_zacatek ? $udalost->datum_zacatek->format('d.m.Y') : '')" placeholder="DD.MM.RRRR" required />
                <x-input-error :messages="$errors->get('datum_zacatek')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="datum_konec" :value="'Datum konce'" />
                <x-text-input id="datum_konec" name="datum_konec" type="text" class="mt-1 block w-full" :value="old('datum_konec', isset($udalost) && $udalost->datum_konec ? $udalost->datum_konec->format('d.m.Y') : '')" placeholder="DD.MM.RRRR" required />
                <x-input-error :messages="$errors->get('datum_konec')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="uzavierka_prihlasek" :value="'Uzávěrka přihlášek'" />
                <x-text-input id="uzavierka_prihlasek" name="uzavierka_prihlasek" type="text" class="mt-1 block w-full" :value="old('uzavierka_prihlasek', isset($udalost) && $udalost->uzavierka_prihlasek ? $udalost->uzavierka_prihlasek->format('d.m.Y') : '')" placeholder="DD.MM.RRRR" required />
                <x-input-error :messages="$errors->get('uzavierka_prihlasek')" class="mt-2" />
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <x-input-label for="kapacita" :value="'Kapacita (volitelně)'" />
                <x-text-input id="kapacita" name="kapacita" type="number" min="1" class="mt-1 block w-full" :value="old('kapacita', $udalost->kapacita ?? '')" />
                <x-input-error :messages="$errors->get('kapacita')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="fotoalbum_url" :value="'Odkaz na fotoalbum (volitelně)'" />
                <x-text-input id="fotoalbum_url" name="fotoalbum_url" type="url" class="mt-1 block w-full" :value="old('fotoalbum_url', $udalost->fotoalbum_url ?? '')" placeholder="https://..." />
                <p class="mt-2 text-sm text-gray-500">Doplňte odkaz později, až budou fotografie dostupné.</p>
                <x-input-error :messages="$errors->get('fotoalbum_url')" class="mt-2" />
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <x-input-label for="propozice_pdf_upload" :value="'Nahrát propozice (PDF, volitelně)'" />
                <label for="propozice_pdf_upload" class="mt-1 flex cursor-pointer items-center justify-between gap-4 rounded-[1.25rem] border border-[#dccdb8] bg-white px-4 py-3 text-sm text-gray-700">
                    <span id="propozice_pdf_upload_label" class="truncate">Žádný soubor nevybrán</span>
                    <span class="rounded-full bg-[#3d6b4f] px-4 py-2 font-semibold text-white">Vyberte soubor</span>
                </label>
                <input
                    id="propozice_pdf_upload"
                    name="propozice_pdf_upload"
                    type="file"
                    accept=".pdf"
                    class="sr-only"
                >
                <x-input-error :messages="$errors->get('propozice_pdf_upload')" class="mt-2" />
                @if($udalost->propozice_pdf)
                    <p class="mt-2 text-sm text-gray-600">
                        Aktuální soubor:
                        <a href="{{ asset('storage/'.$udalost->propozice_pdf) }}" target="_blank" rel="noopener" class="text-[#7b5230] underline underline-offset-4">
                            otevřít propozice
                        </a>
                    </p>
                @endif
            </div>
            <div>
                <x-input-label for="vysledky_pdf_upload" :value="'Nahrát výsledky po závodě (PDF, volitelně)'" />
                <label for="vysledky_pdf_upload" class="mt-1 flex cursor-pointer items-center justify-between gap-4 rounded-[1.25rem] border border-[#dccdb8] bg-white px-4 py-3 text-sm text-gray-700">
                    <span id="vysledky_pdf_upload_label" class="truncate">Žádný soubor nevybrán</span>
                    <span class="rounded-full bg-[#3d6b4f] px-4 py-2 font-semibold text-white">Vyberte soubor</span>
                </label>
                <input
                    id="vysledky_pdf_upload"
                    name="vysledky_pdf_upload"
                    type="file"
                    accept=".pdf"
                    class="sr-only"
                >
                <x-input-error :messages="$errors->get('vysledky_pdf_upload')" class="mt-2" />
                @if($udalost->vysledky_pdf)
                    <p class="mt-2 text-sm text-gray-600">
                        Aktuální soubor:
                        <a href="{{ asset('storage/'.$udalost->vysledky_pdf) }}" target="_blank" rel="noopener" class="text-[#7b5230] underline underline-offset-4">
                            otevřít výsledky
                        </a>
                    </p>
                @endif
            </div>
        </div>

        <div>
            <x-input-label for="popis_editor" :value="'Popis'" />
            <input id="popis" name="popis" type="hidden" value="{{ old('popis', $udalost->popis ?? '') }}">
            <div id="popis_editor" class="mt-1 min-h-[220px] overflow-hidden rounded-[1.25rem] border border-[#dccdb8] bg-white"></div>
            <x-input-error :messages="$errors->get('popis')" class="mt-2" />
        </div>

        <div>
            <label for="aktivni" class="flex items-center gap-3 rounded-[1rem] border border-[#eadfcc] bg-white/60 px-4 py-3 text-sm text-gray-700">
                <input id="aktivni" type="checkbox" name="aktivni" value="1" class="rounded border-[#ccb28f] text-[#3d6b4f] focus:ring-[#3d6b4f]" @checked(old('aktivni', $udalost->aktivni ?? true))>
                <span>Aktivní událost</span>
            </label>
        </div>

        <div class="flex items-center gap-3">
            <x-primary-button>Uložit popis</x-primary-button>
        </div>
    </form>
</section>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css">
<script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const hiddenInput = document.getElementById('popis');
        const editorElement = document.getElementById('popis_editor');
        if (!hiddenInput || !editorElement || editorElement.dataset.quillInitialized === '1' || typeof Quill === 'undefined') {
            return;
        }

        const quill = new Quill(editorElement, {
            theme: 'snow',
            placeholder: 'Zadejte popis události…',
            modules: {
                toolbar: [
                    [{ header: [1, 2, false] }],
                    ['bold', 'italic', 'underline'],
                    [{ list: 'ordered' }, { list: 'bullet' }],
                    ['link'],
                    ['clean'],
                ],
            },
        });

        quill.root.innerHTML = hiddenInput.value || '';
        quill.on('text-change', function () {
            hiddenInput.value = quill.root.innerHTML;
        });
        if (hiddenInput.form) {
            hiddenInput.form.addEventListener('submit', function () {
                hiddenInput.value = quill.root.innerHTML;
            });
        }

        editorElement.dataset.quillInitialized = '1';

        [
            ['propozice_pdf_upload', 'propozice_pdf_upload_label'],
            ['vysledky_pdf_upload', 'vysledky_pdf_upload_label'],
        ].forEach(function ([inputId, labelId]) {
            const input = document.getElementById(inputId);
            const label = document.getElementById(labelId);

            if (!input || !label) {
                return;
            }

            input.addEventListener('change', function () {
                label.textContent = input.files && input.files[0]
                    ? input.files[0].name
                    : 'Žádný soubor nevybrán';
            });
        });
    });
</script>
```

- [ ] **Step 2: Commit**

```bash
git add resources/views/admin/udalosti/_popis.blade.php
git commit -m "feat: create _popis component for event description"
```

---

### Task 9: Create _discipliny.blade.php (Disciplines Management)

**Files:**
- Create: `resources/views/admin/udalosti/_discipliny.blade.php`

- [ ] **Step 1: Create disciplines management component**

```blade
<section class="panel p-6 sm:p-8">
    <div class="space-y-6">
        <div>
            <p class="section-eyebrow">Disciplíny</p>
            <h2 class="mt-2 text-2xl text-[#20392c]">Přidat novou disciplínu</h2>
        </div>

        <form method="POST" action="{{ route('admin.udalosti.moznosti.store', $udalost) }}" class="grid gap-4 md:grid-cols-2" enctype="multipart/form-data">
            @csrf
            <input type="text" name="nazev" placeholder="Název disciplíny" class="field-shell" required>
            <input type="number" name="cena" step="0.01" min="0" placeholder="Cena" class="field-shell" required>
            <input type="number" name="min_vek" min="0" placeholder="Min. věk" class="field-shell">
            <input type="number" name="poradi" min="0" placeholder="Pořadí" class="field-shell">
            
            <div class="md:col-span-2">
                <input type="file" name="foto_path" accept="image/*" placeholder="Fotografie" class="field-shell">
                <p class="mt-1 text-sm text-gray-500">Volitelně: obrázek disciplíny (JPG, PNG, WebP)</p>
            </div>

            <div class="md:col-span-2">
                <input type="file" name="pdf_path" accept=".pdf" placeholder="PDF" class="field-shell">
                <p class="mt-1 text-sm text-gray-500">Volitelně: PDF s informacemi o disciplíně</p>
            </div>

            <label class="md:col-span-2 flex items-center gap-3 rounded-[1rem] border border-[#eadfcc] bg-white/60 px-4 py-3 text-sm text-gray-700">
                <input type="checkbox" name="je_administrativni_poplatek" value="1" class="rounded border-[#ccb28f] text-[#3d6b4f] focus:ring-[#3d6b4f]">
                <span>Administrativní poplatek</span>
            </label>
            <div class="md:col-span-2">
                <button type="submit" class="button-primary">Přidat disciplínu</button>
            </div>
        </form>

        <div class="mt-8 space-y-3">
            <h3 class="font-semibold text-[#20392c]">Stávající disciplíny</h3>
            @forelse($udalost->moznosti as $moznost)
                <div class="surface-muted space-y-3 p-4">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1">
                            <p class="font-semibold text-[#20392c]">{{ $moznost->nazev }}</p>
                            <p class="mt-1 text-sm text-gray-600">
                                {{ number_format((float) $moznost->cena, 2, ',', ' ') }} Kč
                                @if($moznost->min_vek)• min. věk {{ $moznost->min_vek }}@endif
                            </p>
                            @if($moznost->foto_path || $moznost->pdf_path)
                                <div class="mt-2 flex gap-2">
                                    @if($moznost->foto_path)
                                        <a href="{{ asset('storage/'.$moznost->foto_path) }}" target="_blank" rel="noopener" class="text-xs text-[#7b5230] underline">Fotografie</a>
                                    @endif
                                    @if($moznost->pdf_path)
                                        <a href="{{ asset('storage/'.$moznost->pdf_path) }}" target="_blank" rel="noopener" class="text-xs text-[#7b5230] underline">PDF</a>
                                    @endif
                                </div>
                            @endif
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('admin.udalosti.moznosti.edit', [$udalost, $moznost]) }}" class="text-sm text-[#3d6b4f] underline underline-offset-4">Upravit</a>
                            <form method="POST" action="{{ route('admin.udalosti.moznosti.destroy', [$udalost, $moznost]) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Opravdu smazat?')" class="text-sm text-red-700 underline underline-offset-4">Smazat</button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-600">Zatím bez disciplín.</p>
            @endforelse
        </div>
    </div>
</section>
```

- [ ] **Step 2: Commit**

```bash
git add resources/views/admin/udalosti/_discipliny.blade.php
git commit -m "feat: create _discipliny component for discipline management"
```

---

### Task 10: Create _sluzby.blade.php (Services Management)

**Files:**
- Create: `resources/views/admin/udalosti/_sluzby.blade.php`

- [ ] **Step 1: Create services management component**

```blade
<section class="panel p-6 sm:p-8">
    <div class="space-y-6">
        <div>
            <p class="section-eyebrow">Ustájení a služby</p>
            <h2 class="mt-2 text-2xl text-[#20392c]">Přidat novou službu</h2>
        </div>

        <form method="POST" action="{{ route('admin.udalosti.ustajeni.store', $udalost) }}" class="grid gap-4 md:grid-cols-2" enctype="multipart/form-data">
            @csrf
            <input type="text" name="nazev" placeholder="Název položky" class="field-shell" required>
            <select name="typ" class="field-shell" required>
                <option value="">-- Vyberte typ --</option>
                <option value="ustajeni">Ustájení</option>
                <option value="ubytovani">Ubytování</option>
                <option value="strava">Strava</option>
                <option value="ostatni">Ostatní</option>
            </select>
            <input type="number" name="cena" step="0.01" min="0" placeholder="Cena" class="field-shell" required>
            <input type="number" name="kapacita" min="1" placeholder="Kapacita" class="field-shell">
            
            <div class="md:col-span-2">
                <input type="file" name="foto_path" accept="image/*" placeholder="Fotografie" class="field-shell">
                <p class="mt-1 text-sm text-gray-500">Volitelně: obrázek služby (JPG, PNG, WebP)</p>
            </div>

            <div class="md:col-span-2">
                <input type="file" name="pdf_path" accept=".pdf" placeholder="PDF" class="field-shell">
                <p class="mt-1 text-sm text-gray-500">Volitelně: PDF s informacemi o službě</p>
            </div>

            <div class="md:col-span-2">
                <button type="submit" class="button-primary">Přidat službu</button>
            </div>
        </form>

        <div class="mt-8 space-y-3">
            <h3 class="font-semibold text-[#20392c]">Stávající služby</h3>
            @forelse($udalost->ustajeniMoznosti as $moznost)
                <div class="surface-muted space-y-3 p-4">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1">
                            <p class="font-semibold text-[#20392c]">{{ $moznost->nazev }}</p>
                            <p class="mt-1 text-sm text-gray-600">
                                {{ $moznost->typ }}
                                • {{ number_format((float) $moznost->cena, 2, ',', ' ') }} Kč
                                @if($moznost->kapacita)• kapacita {{ $moznost->kapacita }}@endif
                            </p>
                            @if($moznost->foto_path || $moznost->pdf_path)
                                <div class="mt-2 flex gap-2">
                                    @if($moznost->foto_path)
                                        <a href="{{ asset('storage/'.$moznost->foto_path) }}" target="_blank" rel="noopener" class="text-xs text-[#7b5230] underline">Fotografie</a>
                                    @endif
                                    @if($moznost->pdf_path)
                                        <a href="{{ asset('storage/'.$moznost->pdf_path) }}" target="_blank" rel="noopener" class="text-xs text-[#7b5230] underline">PDF</a>
                                    @endif
                                </div>
                            @endif
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('admin.udalosti.ustajeni.edit', [$udalost, $moznost]) }}" class="text-sm text-[#3d6b4f] underline underline-offset-4">Upravit</a>
                            <form method="POST" action="{{ route('admin.udalosti.ustajeni.destroy', [$udalost, $moznost]) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Opravdu smazat?')" class="text-sm text-red-700 underline underline-offset-4">Smazat</button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-600">Zatím bez služeb.</p>
            @endforelse
        </div>
    </div>
</section>
```

- [ ] **Step 2: Commit**

```bash
git add resources/views/admin/udalosti/_sluzby.blade.php
git commit -m "feat: create _sluzby component for service management"
```

---

### Task 11: Replace edit.blade.php with Tab Layout

**Files:**
- Modify: `resources/views/admin/udalosti/edit.blade.php`

- [ ] **Step 1: Replace entire file**

```blade
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div class="space-y-3">
                <p class="section-eyebrow">Nastavení události</p>
                <h1 class="text-3xl text-[#20392c]">{{ $udalost->nazev }}</h1>
                <p class="max-w-3xl text-sm leading-6 text-gray-600">Spravujte popis, disciplíny, služby a přihlášky na jednom místě.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl space-y-6">
            @include('admin.udalosti._tabs', ['udalost' => $udalost, 'active' => 'popis'])

            <div x-data="{ activeTab: 'popis' }" x-init="activeTab = window.location.hash.slice(1) || 'popis'">
                <!-- Popis Tab -->
                <div x-show="activeTab === 'popis'" @click="window.location.hash = 'popis'">
                    @include('admin.udalosti._popis', ['udalost' => $udalost])
                </div>

                <!-- Disciplíny Tab -->
                <div x-show="activeTab === 'discipliny'" @click="window.location.hash = 'discipliny'">
                    @include('admin.udalosti._discipliny', ['udalost' => $udalost])
                </div>

                <!-- Služby Tab -->
                <div x-show="activeTab === 'sluzby'" @click="window.location.hash = 'sluzby'">
                    @include('admin.udalosti._sluzby', ['udalost' => $udalost])
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
```

- [ ] **Step 2: Commit**

```bash
git add resources/views/admin/udalosti/edit.blade.php
git commit -m "feat: refactor edit.blade.php with tab-based layout"
```

---

### Task 12: Delete Unused _form.blade.php

**Files:**
- Delete: `resources/views/admin/udalosti/_form.blade.php`

- [ ] **Step 1: Delete the file**

Run: `rm resources/views/admin/udalosti/_form.blade.php`

- [ ] **Step 2: Commit**

```bash
git add -u resources/views/admin/udalosti/
git commit -m "feat: remove _form.blade.php (content moved to _popis.blade.php)"
```

---

### Task 13: Update Request Validation Classes

**Files:**
- Modify: `app/Http/Requests/Admin/StoreAdminUdalostMoznostRequest.php` (if exists)
- Modify: `app/Http/Requests/Admin/StoreAdminUdalostUstajeniRequest.php` (if exists)

- [ ] **Step 1: Check if request classes exist**

Run: `ls app/Http/Requests/Admin/`

- [ ] **Step 2: Update to accept file uploads**

If files exist, update to include:
```php
'foto_path' => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:5120'],
'pdf_path' => ['nullable', 'mimes:pdf', 'max:5120'],
'popis_text' => ['nullable', 'string'],
'popis_html' => ['nullable', 'string'],
```

- [ ] **Step 3: Commit if changes made**

```bash
git add app/Http/Requests/Admin/
git commit -m "feat: add file validation to discipline and service requests"
```

---

### Task 14: Run Tests

**Files:**
- Test: `tests/Feature/Admin/AdminReportFlowTest.php` (or create new test)

- [ ] **Step 1: Run full test suite**

Run: `php artisan test`
Expected: All tests pass

- [ ] **Step 2: Commit if any test changes needed**

If tests need updates for new routes/functionality:
```bash
git add tests/
git commit -m "test: update admin event tests for new tab structure"
```

---

## Summary

This plan implements:

1. **Database**: New columns for file storage and descriptions on disciplines and services
2. **Models**: Updated to handle new fields
3. **Routes**: New edit/update routes for disciplines and services
4. **Controller**: Methods to handle editing and updating disciplines and services with file uploads
5. **Views**: 
   - Refactored edit page with tab-based layout
   - Moved description form to dedicated component
   - Separated disciplines and services into dedicated tabs
   - Added edit functionality with file upload support
   - Removed unnecessary "Přehled" section

**Files Changed**: 14 total
- 1 new migration
- 2 modified models
- 1 modified controller
- 1 modified routes file
- 1 modified tabs component
- 3 new view components
- 1 modified edit view
- 1 deleted view
- Up to 2 modified request classes
- Tests updated as needed
