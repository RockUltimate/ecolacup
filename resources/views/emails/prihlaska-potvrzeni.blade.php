<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Potvrzení přihlášky</title>
</head>
<body style="font-family: Arial, sans-serif; color: #1f2937; line-height: 1.5;">
    <h2 style="margin-bottom: 8px;">Potvrzení přihlášky #{{ $prihlaska->id }}</h2>
    <p style="margin-top: 0;">
        Děkujeme, vaše přihláška na událost <strong>{{ $prihlaska->udalost?->nazev }}</strong> byla úspěšně přijata.
    </p>
    <p>
        <strong>Účastník:</strong> {{ $prihlaska->osoba?->prijmeni }} {{ $prihlaska->osoba?->jmeno }}<br>
        <strong>Kůň:</strong> {{ $prihlaska->kun?->jmeno }} @if($prihlaska->kunTandem) + {{ $prihlaska->kunTandem->jmeno }} @endif<br>
        <strong>Celkem:</strong> {{ number_format((float) $prihlaska->cena_celkem, 2, ',', ' ') }} Kč
    </p>
    <p>
        Detail přihlášky:
        <a href="{{ route('prihlasky.show', $prihlaska) }}">{{ route('prihlasky.show', $prihlaska) }}</a>
    </p>
    <p>V příloze najdete PDF přihlášky.</p>
    <p style="margin-top: 24px; color: #6b7280; font-size: 12px;">koneakce.cz</p>
</body>
</html>
