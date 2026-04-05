<x-app-layout>
    @php
        $filters = $filters ?? ['q' => ''];
    @endphp
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Admin • Uživatelé</h2>
            <a href="{{ route('admin.dashboard') }}" class="text-sm text-indigo-600 hover:text-indigo-800 underline">Dashboard</a>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <x-flash-message />
            <x-admin-report-filter-form :action="route('admin.users.index')" :reset-href="route('admin.users.index')">
                <div>
                    <x-input-label for="q" :value="'Hledat (jméno, příjmení, e-mail)'" />
                    <x-text-input id="q" name="q" type="text" class="mt-1 block w-full" :value="$filters['q']" />
                </div>
            </x-admin-report-filter-form>

            <div class="bg-white shadow sm:rounded-lg overflow-hidden">
                <div class="divide-y divide-gray-200">
                    @forelse($users as $user)
                        <div class="p-4 sm:p-5 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="font-medium text-gray-900">
                                    {{ $user->prijmeni }} {{ $user->jmeno }}
                                    @if($user->is_admin)
                                        <span class="ms-2 inline-flex rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-semibold text-emerald-700">ADMIN</span>
                                    @endif
                                </p>
                                <p class="text-sm text-gray-600">{{ $user->email }} @if($user->telefon) • {{ $user->telefon }} @endif</p>
                                <p class="text-xs text-gray-500">
                                    Osoby: {{ $user->osoby_count }} • Koně: {{ $user->kone_count }} • Přihlášky: {{ $user->prihlasky_count }}
                                </p>
                            </div>
                            <a href="{{ route('admin.users.edit', $user) }}" class="text-sm text-indigo-600 hover:text-indigo-800 underline">Upravit</a>
                        </div>
                    @empty
                        <div class="p-5 text-sm text-gray-600">Žádní uživatelé nenalezeni.</div>
                    @endforelse
                </div>
            </div>

            <div>
                {{ $users->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
