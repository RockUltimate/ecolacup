@php
    $isEdit = isset($udalost);
@endphp

<form method="POST" action="{{ $isEdit ? route('admin.udalosti.update', $udalost) : route('admin.udalosti.store') }}" class="space-y-6" enctype="multipart/form-data">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

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
            <x-text-input id="datum_zacatek" name="datum_zacatek" type="date" class="mt-1 block w-full" :value="old('datum_zacatek', isset($udalost) && $udalost->datum_zacatek ? $udalost->datum_zacatek->format('Y-m-d') : '')" required />
            <x-input-error :messages="$errors->get('datum_zacatek')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="datum_konec" :value="'Datum konce'" />
            <x-text-input id="datum_konec" name="datum_konec" type="date" class="mt-1 block w-full" :value="old('datum_konec', isset($udalost) && $udalost->datum_konec ? $udalost->datum_konec->format('Y-m-d') : '')" required />
            <x-input-error :messages="$errors->get('datum_konec')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="uzavierka_prihlasek" :value="'Uzávěrka přihlášek'" />
            <x-text-input id="uzavierka_prihlasek" name="uzavierka_prihlasek" type="date" class="mt-1 block w-full" :value="old('uzavierka_prihlasek', isset($udalost) && $udalost->uzavierka_prihlasek ? $udalost->uzavierka_prihlasek->format('Y-m-d') : '')" required />
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
            <x-input-label for="propozice_pdf_upload" :value="'Nahrát propozice (PDF, volitelně)'" />
            <input
                id="propozice_pdf_upload"
                name="propozice_pdf_upload"
                type="file"
                accept=".pdf"
                class="mt-1 block w-full text-sm text-gray-700 file:mr-3 file:rounded-md file:border-0 file:bg-[#3d6b4f] file:px-3 file:py-2 file:text-white hover:file:bg-[#31563f]"
            >
            <x-input-error :messages="$errors->get('propozice_pdf_upload')" class="mt-2" />
            @if($isEdit && $udalost->propozice_pdf)
                <p class="mt-2 text-sm text-gray-600">
                    Aktuální soubor:
                    <a href="{{ asset('storage/'.$udalost->propozice_pdf) }}" target="_blank" rel="noopener" class="text-indigo-600 hover:text-indigo-800 underline">
                        otevřít propozice
                    </a>
                </p>
            @endif
        </div>
    </div>

    <div>
        <x-input-label for="popis_editor" :value="'Popis'" />
        <input id="popis" name="popis" type="hidden" value="{{ old('popis', $udalost->popis ?? '') }}">
        <div id="popis_editor" class="mt-1 rounded-md border border-gray-300 bg-white min-h-[220px]"></div>
        <x-input-error :messages="$errors->get('popis')" class="mt-2" />
    </div>

    <div>
        <label for="aktivni" class="inline-flex items-center">
            <input id="aktivni" type="checkbox" name="aktivni" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" @checked(old('aktivni', $udalost->aktivni ?? true))>
            <span class="ms-2 text-sm text-gray-700">Aktivní událost</span>
        </label>
    </div>

    <div class="flex items-center gap-3">
        <x-primary-button>{{ $isEdit ? 'Uložit událost' : 'Vytvořit událost' }}</x-primary-button>
        <a href="{{ route('admin.udalosti.index') }}" class="text-sm text-gray-600 hover:text-gray-900 underline">Zpět na seznam</a>
    </div>
</form>

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
    });
</script>
