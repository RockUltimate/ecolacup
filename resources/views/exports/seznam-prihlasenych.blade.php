<html>
<head>
    <meta charset="utf-8">
    @include('exports._styles')
</head>
<body>
<table cellspacing="5" cellpadding="5">
    <tr style="background-color:#549ee9;">
        <td style="text-align:center;">st.č.</td>
        <td style="width:180px;">Jméno osoba</td>
        <td>Jméno a pohlaví koně + kůň tandem ruka</td>
        <td>Stáj</td>
        <td>Datum narození</td>
        <td>Cena celkem</td>
    </tr>
    @foreach($prihlasky as $p)
        <tr @if($p->smazana) style="background-color:#ffcccc;" @endif>
            <td style="text-align:center;">{{ $p->start_cislo }}</td>
            <td>{{ $p->osoba?->prijmeni }} {{ $p->osoba?->jmeno }}{{ $p->vekKategorie() }}</td>
            <td>
                {{ $p->kun?->jmeno }} ({{ $p->kun?->plemeno_kod }}, {{ $p->kun?->rok_narozeni }}, {{ $p->kun?->pohlavi }})
                @if($p->kunTandem)
                    , {{ $p->kunTandem->jmeno }} ({{ $p->kunTandem->plemeno_kod }}, {{ $p->kunTandem->rok_narozeni }}, {{ $p->kunTandem->pohlavi }})
                @endif
            </td>
            <td>{{ $p->osoba?->staj }}</td>
            <td>
                {{ $p->osoba?->datum_narozeni?->format('d.m.Y') }}
                @if($p->osoba?->datum_narozeni)
                    <span style="font-size:10px;">({{ $p->osoba->datum_narozeni->age }} let)</span>
                @endif
            </td>
            <td style="text-align:right;">{{ number_format((float)$p->cena_celkem, 2, ',', ' ') }}</td>
        </tr>
    @endforeach
</table>
</body>
</html>
