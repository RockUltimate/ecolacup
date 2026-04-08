<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Přihláška {{ $prihlaska->id }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; }
        h1 { font-size: 18px; margin: 0 0 12px 0; }
        h2 { font-size: 14px; margin: 16px 0 6px 0; }
        .muted { color: #555; }
        .box { border: 1px solid #ddd; padding: 10px; margin-bottom: 10px; }
        .row { margin-bottom: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 6px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background: #f5f5f5; }
        .right { text-align: right; }
        .total { font-size: 14px; font-weight: bold; margin-top: 10px; text-align: right; }
    </style>
</head>
<body>
    <h1>Přihláška #{{ $prihlaska->id }}</h1>
    <div class="muted">Vygenerováno: {{ now()->format('d.m.Y H:i') }}</div>

    <h2>Událost</h2>
    <div class="box">
        <div class="row"><strong>Název:</strong> {{ $prihlaska->udalost?->nazev }}</div>
        <div class="row"><strong>Místo:</strong> {{ $prihlaska->udalost?->misto }}</div>
        <div class="row"><strong>Termín:</strong> {{ $prihlaska->udalost?->datum_zacatek?->format('d.m.Y') }}</div>
    </div>

    <h2>Účastník a kůň</h2>
    <div class="box">
        <div class="row"><strong>Osoba:</strong> {{ $prihlaska->osoba?->prijmeni }} {{ $prihlaska->osoba?->jmeno }}{{ $prihlaska->vekKategorie() }}</div>
        <div class="row"><strong>Stáj:</strong> {{ $prihlaska->osoba?->staj }}</div>
        <div class="row"><strong>Kůň:</strong> {{ $prihlaska->kun?->jmeno }}</div>
        @if($prihlaska->kunTandem)
            <div class="row"><strong>Tandem:</strong> {{ $prihlaska->kunTandem->jmeno }}</div>
        @endif
    </div>

    <h2>Disciplíny</h2>
    <table>
        <thead>
            <tr>
                <th>Položka</th>
                <th class="right">Cena</th>
            </tr>
        </thead>
        <tbody>
            @foreach($prihlaska->polozky as $item)
                <tr>
                    <td>{{ $item->nazev }}</td>
                    <td class="right">{{ number_format((float)$item->cena, 2, ',', ' ') }} Kč</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Ustájení / ubytování</h2>
    <table>
        <thead>
            <tr>
                <th>Položka</th>
                <th class="right">Cena</th>
            </tr>
        </thead>
        <tbody>
            @forelse($prihlaska->ustajeniChoices as $item)
                <tr>
                    <td>{{ $item->ustajeni?->nazev }}</td>
                    <td class="right">{{ number_format((float)$item->cena, 2, ',', ' ') }} Kč</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2">Bez doplňkových položek</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if($prihlaska->poznamka)
        <h2>Poznámka</h2>
        <div class="box">{{ $prihlaska->poznamka }}</div>
    @endif

    <div class="total">Celkem: {{ number_format((float)$prihlaska->cena_celkem, 2, ',', ' ') }} Kč</div>
</body>
</html>
