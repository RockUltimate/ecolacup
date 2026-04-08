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
        <div
            class="mx-auto max-w-7xl space-y-6"
            x-data="{
                allowedTabs: ['popis', 'discipliny', 'sluzby'],
                activeTab: 'popis',
                syncFromHash() {
                    const hash = window.location.hash.replace('#', '');
                    this.activeTab = this.allowedTabs.includes(hash) ? hash : 'popis';
                },
                openTab(tab) {
                    this.activeTab = tab;
                    window.location.hash = tab;
                }
            }"
            x-init="syncFromHash(); window.addEventListener('hashchange', () => syncFromHash())"
        >
            <div class="panel p-3">
                <nav class="flex flex-wrap gap-2">
                    <a
                        href="{{ route('admin.udalosti.edit', $udalost) }}#popis"
                        @click.prevent="openTab('popis')"
                        class="rounded-full border px-4 py-2 text-sm font-semibold transition"
                        :class="activeTab === 'popis'
                            ? 'border-[#20392c] bg-[#20392c] text-white'
                            : 'border-[#ddd0bc] bg-white/70 text-[#3d6b4f] hover:bg-emerald-50'"
                    >
                        Popis
                    </a>
                    <a
                        href="{{ route('admin.udalosti.edit', $udalost) }}#discipliny"
                        @click.prevent="openTab('discipliny')"
                        class="rounded-full border px-4 py-2 text-sm font-semibold transition"
                        :class="activeTab === 'discipliny'
                            ? 'border-[#20392c] bg-[#20392c] text-white'
                            : 'border-[#ddd0bc] bg-white/70 text-[#3d6b4f] hover:bg-emerald-50'"
                    >
                        Disciplíny
                    </a>
                    <a
                        href="{{ route('admin.udalosti.edit', $udalost) }}#sluzby"
                        @click.prevent="openTab('sluzby')"
                        class="rounded-full border px-4 py-2 text-sm font-semibold transition"
                        :class="activeTab === 'sluzby'
                            ? 'border-[#20392c] bg-[#20392c] text-white'
                            : 'border-[#ddd0bc] bg-white/70 text-[#3d6b4f] hover:bg-emerald-50'"
                    >
                        Služby
                    </a>
                    <a
                        href="{{ route('admin.reports.prihlasky', $udalost) }}"
                        class="rounded-full border border-[#ddd0bc] bg-white/70 px-4 py-2 text-sm font-semibold text-[#3d6b4f] transition hover:bg-emerald-50"
                    >
                        Přihlášky
                    </a>
                    <a
                        href="{{ route('admin.reports.startky', $udalost) }}"
                        class="rounded-full border border-[#ddd0bc] bg-white/70 px-4 py-2 text-sm font-semibold text-[#3d6b4f] transition hover:bg-emerald-50"
                    >
                        Startky
                    </a>
                </nav>
            </div>

            <div>
                <div x-show="activeTab === 'popis'">
                    @include('admin.udalosti._popis', ['udalost' => $udalost])
                </div>

                <div x-show="activeTab === 'discipliny'">
                    @include('admin.udalosti._discipliny', ['udalost' => $udalost])
                </div>

                <div x-show="activeTab === 'sluzby'">
                    @include('admin.udalosti._sluzby', ['udalost' => $udalost])
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
