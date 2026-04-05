<x-app-layout>
    @php
        $filters = $filters ?? ['q' => ''];
    @endphp
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-on-surface dark:text-[#e5e2dd] leading-tight">Admin • Uživatelé</h2>
            <a href="{{ route('admin.dashboard') }}" class="text-sm text-primary dark:text-inverse-primary hover:underline underline">Dashboard</a>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <x-flash-message />
            <x-admin-report-filter-form :action="route('admin.users.index')" :reset-href="route('admin.users.index')">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant dark:text-[#c3c8bb]">Hledat (jméno, příjmení, e-mail)</label>
                    <input id="q" name="q" type="text" class="field-shell mt-1 block w-full" value="{{ $filters['q'] }}" />
                </div>
            </x-admin-report-filter-form>

            <div class="bg-surface-container-lowest dark:bg-[#252522] shadow sm:rounded-lg overflow-hidden">
                <div class="divide-y divide-gray-200">
                    @forelse($users as $user)
                        <div class="p-4 sm:p-5 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="font-medium text-on-surface dark:text-[#e5e2dd]">
                                    {{ $user->prijmeni }} {{ $user->jmeno }}
                                    @if($user->is_admin)
                                        <span class="ms-2 inline-flex rounded-full bg-primary-fixed px-2 py-0.5 text-xs font-semibold text-on-primary-fixed">ADMIN</span>
                                    @endif
                                </p>
                                <p class="text-sm text-on-surface-variant dark:text-[#c3c8bb]">{{ $user->email }} @if($user->telefon) • {{ $user->telefon }} @endif</p>
                                <p class="text-xs text-on-surface-variant dark:text-[#c3c8bb]">
                                    Osoby: {{ $user->osoby_count }} • Koně: {{ $user->kone_count }} • Přihlášky: {{ $user->prihlasky_count }}
                                </p>
                            </div>
                            <a href="{{ route('admin.users.edit', $user) }}" class="text-sm text-primary dark:text-inverse-primary hover:underline underline">Upravit</a>
                        </div>
                    @empty
                        <div class="p-5 text-sm text-on-surface-variant dark:text-[#c3c8bb]">Žádní uživatelé nenalezeni.</div>
                    @endforelse
                </div>
            </div>

            <div>
                {{ $users->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
