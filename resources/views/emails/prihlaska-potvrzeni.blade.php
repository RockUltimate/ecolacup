<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>{{ $mode === 'updated' ? 'Aktualizace přihlášky' : 'Potvrzení přihlášky' }}</title>
</head>
<body style="font-family: Arial, sans-serif; color: #1f2937; line-height: 1.5;">
    <h2 style="margin-bottom: 8px;">{{ $mode === 'updated' ? 'Aktualizace přihlášky' : 'Potvrzení přihlášky' }} #{{ $prihlaska->id }}</h2>
    <p style="margin-top: 0;">
        @if($mode === 'updated')
            Vaše přihláška na událost <strong>{{ $prihlaska->udalost?->nazev }}</strong> byla upravena. V příloze posíláme aktuální PDF verzi.
        @else
            Děkujeme, vaše přihláška na událost <strong>{{ $prihlaska->udalost?->nazev }}</strong> byla úspěšně přijata.
        @endif
    </p>
    <p>
        <strong>Účastník:</strong> {{ $prihlaska->osoba?->prijmeni }} {{ $prihlaska->osoba?->jmeno }}<br>
        <strong>Kůň:</strong> {{ $prihlaska->kun?->jmeno }} @if($prihlaska->kunTandem) + {{ $prihlaska->kunTandem->jmeno }} @endif<br>
        @php
            $adminFeePolozka = $prihlaska->polozky->first(fn ($p) => $p->moznost?->je_administrativni_poplatek);
        @endphp
        <strong>Administrativní poplatek:</strong>
        @if($adminFeePolozka)
            {{ number_format((float) $adminFeePolozka->cena, 2, ',', ' ') }} Kč
        @else
            0,00 Kč (již uhrazeno v jiné přihlášce)
        @endif<br>
        <strong>Celkem:</strong> {{ number_format((float) $prihlaska->cena_celkem, 2, ',', ' ') }} Kč
    </p>
    <p>
        Detail přihlášky:
        <a href="{{ route('prihlasky.show', $prihlaska) }}">{{ route('prihlasky.show', $prihlaska) }}</a>
    </p>
    <p>V příloze najdete aktuální PDF přihlášky.</p>
    <p style="margin-top: 24px; color: #6b7280; font-size: 12px;">koneakce.cz</p>
</body>
</html>
