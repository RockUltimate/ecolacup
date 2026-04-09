<x-app-layout>
    <x-slot name="header">
        <div class="space-y-3">
            <p class="section-eyebrow">Nastavení události</p>
            <h1 class="text-3xl text-[#20392c]">{{ $udalost->nazev }}</h1>
            <p class="max-w-3xl text-sm leading-6 text-gray-600">Spravujte popis, disciplíny, služby, přihlášky, startky i exporty na jednom místě.</p>
        </div>
    </x-slot>
    <x-slot name="headerActions">
        <a href="{{ route('admin.udalosti.index') }}" class="button-secondary w-full">Zpět na události</a>
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
            <div class="admin-tab-strip">
                <nav class="admin-tab-nav">
                    <a
                        href="{{ route('admin.udalosti.edit', $udalost) }}#popis"
                        @click.prevent="openTab('popis')"
                        class="admin-tab"
                        :class="activeTab === 'popis'
                            ? 'admin-tab--active'
                            : 'admin-tab--inactive'"
                    >
                        Popis
                    </a>
                    <a
                        href="{{ route('admin.udalosti.edit', $udalost) }}#discipliny"
                        @click.prevent="openTab('discipliny')"
                        class="admin-tab"
                        :class="activeTab === 'discipliny'
                            ? 'admin-tab--active'
                            : 'admin-tab--inactive'"
                    >
                        Disciplíny
                    </a>
                    <a
                        href="{{ route('admin.udalosti.edit', $udalost) }}#sluzby"
                        @click.prevent="openTab('sluzby')"
                        class="admin-tab"
                        :class="activeTab === 'sluzby'
                            ? 'admin-tab--active'
                            : 'admin-tab--inactive'"
                    >
                        Služby
                    </a>
                    <a
                        href="{{ route('admin.reports.prihlasky', $udalost) }}"
                        class="admin-tab admin-tab--inactive"
                    >
                        Přihlášky
                    </a>
                    <a
                        href="{{ route('admin.reports.startky', $udalost) }}"
                        class="admin-tab admin-tab--inactive"
                    >
                        Startky
                    </a>
                    <a
                        href="{{ route('admin.reports.exporty', $udalost) }}"
                        class="admin-tab admin-tab--inactive"
                    >
                        Exporty
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
