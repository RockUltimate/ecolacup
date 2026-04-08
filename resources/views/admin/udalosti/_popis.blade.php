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
