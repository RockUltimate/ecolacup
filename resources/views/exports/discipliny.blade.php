<html>
<head>
    <meta charset="utf-8">
    @include('exports._styles')
</head>
<body>
<table border="1" cellspacing="0" cellpadding="4">
    <tr>
        <td>č.</td>
        <td>Jméno</td>
        <td>Kůň</td>
        <td>pozn.</td>
        @foreach($moznosti as $moz)
            <td>
                <table>
                    <tr><td>{{ $moz->nazev }}</td></tr>
                    <tr><td>{{ number_format((float)$moz->cena, 2, ',', ' ') }}</td></tr>
                </table>
            </td>
        @endforeach
        @foreach($ustajeniOptions as $u)
            <td>{{ $u->nazev }} ({{ (int)$u->cena }} Kč)</td>
        @endforeach
    </tr>
    @foreach($prihlasky as $p)
        <tr>
            <td>{{ $p->start_cislo }}</td>
            <td>{{ $p->osoba?->prijmeni }} {{ $p->osoba?->jmeno }}</td>
            <td>{{ $p->kun?->jmeno }}</td>
            <td>{{ $p->poznamka }}</td>
            @foreach($moznosti as $moz)
                <td style="text-align:center;">{{ $p->polozky->contains('moznost_id', $moz->id) ? 'X' : '' }}</td>
            @endforeach
            @foreach($ustajeniOptions as $u)
                <td style="text-align:center;">{{ $p->ustajeniChoices->contains('ustajeni_id', $u->id) ? 'X' : '' }}</td>
            @endforeach
        </tr>
    @endforeach
</table>
</body>
</html>
