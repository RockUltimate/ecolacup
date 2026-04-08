<x-app-layout>
    <x-slot name="header">
        <div class="space-y-3">
            <p class="section-eyebrow">Administrace</p>
            <h1 class="text-3xl text-[#20392c]">Správa systému</h1>
            <p class="max-w-3xl text-sm leading-6 text-gray-600">Vyberte sekci, kterou chcete spravovat.</p>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl">
            <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <a href="{{ route('admin.udalosti.index') }}" class="panel block p-6 transition duration-200 hover:-translate-y-0.5 hover:bg-white/80 hover:shadow-[0_26px_80px_rgba(71,52,34,0.12)]">
                    <p class="section-eyebrow">Admin</p>
                    <h2 class="mt-3 text-2xl text-[#20392c]">Události</h2>
                    <p class="mt-2 text-sm leading-6 text-gray-600">Správa akcí, disciplín, služeb a reportů.</p>
                </a>

                <a href="{{ route('admin.users.index') }}" class="panel block p-6 transition duration-200 hover:-translate-y-0.5 hover:bg-white/80 hover:shadow-[0_26px_80px_rgba(71,52,34,0.12)]">
                    <p class="section-eyebrow">Admin</p>
                    <h2 class="mt-3 text-2xl text-[#20392c]">Uživatelé</h2>
                    <p class="mt-2 text-sm leading-6 text-gray-600">Přehled účtů a jejich dat.</p>
                </a>

                <a href="{{ route('admin.kone.index') }}" class="panel block p-6 transition duration-200 hover:-translate-y-0.5 hover:bg-white/80 hover:shadow-[0_26px_80px_rgba(71,52,34,0.12)]">
                    <p class="section-eyebrow">Admin</p>
                    <h2 class="mt-3 text-2xl text-[#20392c]">Koně</h2>
                    <p class="mt-2 text-sm leading-6 text-gray-600">Všichni koně, jejich vlastníci a duplicity.</p>
                </a>

                <a href="{{ route('admin.osoby.index') }}" class="panel block p-6 transition duration-200 hover:-translate-y-0.5 hover:bg-white/80 hover:shadow-[0_26px_80px_rgba(71,52,34,0.12)]">
                    <p class="section-eyebrow">Admin</p>
                    <h2 class="mt-3 text-2xl text-[#20392c]">Osoby</h2>
                    <p class="mt-2 text-sm leading-6 text-gray-600">Všechny osoby a jejich přiřazení k uživatelům.</p>
                </a>
            </section>
        </div>
    </div>
</x-app-layout>
