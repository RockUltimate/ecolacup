<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Nová přihláška</title>
</head>
<body style="font-family: Arial, sans-serif; color: #1f2937; line-height: 1.5;">
    <h2 style="margin-bottom: 8px;">Nová přihláška #{{ $prihlaska->id }}</h2>
    <p style="margin-top: 0;">
        Byla podána nová přihláška na událost <strong>{{ $prihlaska->udalost?->nazev }}</strong>.
    </p>

    <table style="border-collapse: collapse; width: 100%; max-width: 560px;">
        <tr>
            <td style="padding: 6px 12px 6px 0; font-weight: bold; white-space: nowrap;">Účastník</td>
            <td style="padding: 6px 0;">{{ $prihlaska->osoba?->prijmeni }} {{ $prihlaska->osoba?->jmeno }}</td>
        </tr>
        <tr>
            <td style="padding: 6px 12px 6px 0; font-weight: bold; white-space: nowrap;">Datum narození</td>
            <td style="padding: 6px 0;">{{ $prihlaska->osoba?->datum_narozeni?->format('d. m. Y') ?? '–' }}</td>
        </tr>
        <tr>
            <td style="padding: 6px 12px 6px 0; font-weight: bold; white-space: nowrap;">Stáj</td>
            <td style="padding: 6px 0;">{{ $prihlaska->osoba?->staj ?? '–' }}</td>
        </tr>
        <tr>
            <td style="padding: 6px 12px 6px 0; font-weight: bold; white-space: nowrap;">Kůň</td>
            <td style="padding: 6px 0;">
                {{ $prihlaska->kun?->jmeno }}
                @if($prihlaska->kunTandem) + {{ $prihlaska->kunTandem->jmeno }} @endif
            </td>
        </tr>
        <tr>
            <td style="padding: 6px 12px 6px 0; font-weight: bold; white-space: nowrap;">Disciplíny</td>
            <td style="padding: 6px 0;">
                @forelse($prihlaska->polozky as $polozka)
                    {{ $polozka->nazev }}@if(!$loop->last), @endif
                @empty
                    –
                @endforelse
            </td>
        </tr>
        @if($prihlaska->ustajeniChoices->isNotEmpty())
        <tr>
            <td style="padding: 6px 12px 6px 0; font-weight: bold; white-space: nowrap;">Ustájení</td>
            <td style="padding: 6px 0;">
                @foreach($prihlaska->ustajeniChoices as $choice)
                    {{ $choice->ustajeni?->nazev }}@if(!$loop->last), @endif
                @endforeach
            </td>
        </tr>
        @endif
        <tr>
            <td style="padding: 6px 12px 6px 0; font-weight: bold; white-space: nowrap;">Celková cena</td>
            <td style="padding: 6px 0;">{{ number_format((float) $prihlaska->cena_celkem, 2, ',', ' ') }} Kč</td>
        </tr>
        @if($prihlaska->poznamka)
        <tr>
            <td style="padding: 6px 12px 6px 0; font-weight: bold; white-space: nowrap;">Poznámka</td>
            <td style="padding: 6px 0;">{{ $prihlaska->poznamka }}</td>
        </tr>
        @endif
        <tr>
            <td style="padding: 6px 12px 6px 0; font-weight: bold; white-space: nowrap;">Kontaktní e-mail</td>
            <td style="padding: 6px 0;">{{ $prihlaska->user?->email }}</td>
        </tr>
        <tr>
            <td style="padding: 6px 12px 6px 0; font-weight: bold; white-space: nowrap;">Telefon</td>
            <td style="padding: 6px 0;">{{ $prihlaska->user?->telefon ?? '–' }}</td>
        </tr>
    </table>

    <p style="margin-top: 20px;">
        <a href="{{ route('admin.udalosti.prihlasky.index', ['udalost' => $prihlaska->udalost_id]) }}">Zobrazit v administraci</a>
    </p>

    <p style="margin-top: 24px; color: #6b7280; font-size: 12px;">Ecolakoně – automatická notifikace</p>
</body>
</html>
