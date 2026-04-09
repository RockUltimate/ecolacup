<x-app-layout>
    @php
        $filters = $filters ?? ['q' => ''];
    @endphp

    <x-slot name="header">
        <div class="space-y-3">
            <p class="section-eyebrow">Admin • Uživatelé</p>
            <h1 class="text-3xl text-[#20392c]">Všichni uživatelé</h1>
            <p class="max-w-3xl text-sm leading-6 text-gray-600">Centrální přehled uživatelských účtů včetně navázaných osob, koní a přihlášek.</p>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl space-y-6">
            <x-flash-message />

            <x-admin-report-filter-form :action="route('admin.users.index')" :reset-href="route('admin.users.index')">
                <div>
                    <x-input-label for="q" :value="'Hledat (jméno, příjmení, e-mail)'" />
                    <x-text-input id="q" name="q" type="text" class="mt-1 block w-full" :value="$filters['q']" />
                </div>
            </x-admin-report-filter-form>

            <section class="panel overflow-hidden">
                <div class="divide-y divide-[#eadfcc]">
                    @forelse($users as $user)
                        <div class="p-5">
                            <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
                                <div class="space-y-2">
                                    <div class="flex flex-wrap items-center gap-3">
                                        <p class="text-lg font-semibold text-[#20392c]">{{ $user->prijmeni }} {{ $user->jmeno }}</p>
                                        @if($user->is_admin)
                                            <span class="brand-pill">Admin</span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-600">{{ $user->email }} @if($user->telefon) • {{ $user->telefon }} @endif</p>
                                    <p class="text-xs text-gray-500">Osoby: {{ $user->osoby_count }} • Koně: {{ $user->kone_count }} • Přihlášky: {{ $user->prihlasky_count }}</p>
                                </div>

                                <div class="flex w-[170px] max-w-full flex-col gap-3">
                                    <a href="{{ route('admin.users.edit', $user) }}" class="button-secondary w-full">Upravit</a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-5 text-sm text-gray-600">Žádní uživatelé nenalezeni.</div>
                    @endforelse
                </div>
            </section>

            <div>
                {{ $users->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
