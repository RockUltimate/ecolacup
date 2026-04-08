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
