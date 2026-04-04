<html>
<head><meta charset="utf-8">@include('exports._styles')</head>
<body>
@foreach(['ustajeni' => 'Ustajeni', 'ubytovani' => 'Ubytovani', 'ostatni' => 'Ostatní', 'strava' => 'Strava'] as $typ => $label)
    <h1 style="color:red;">{{ $label }}</h1>
    @foreach(($ustajeniByTyp[$typ] ?? collect()) as $entry)
        <h2>{{ $entry->option->nazev }}</h2>
        <table cellspacing="0" cellpadding="5" border="1" width="704">
            <tr>
                <td style="width:42px;text-align:center;">st.č.</td>
                <td style="width:170px;">jméno</td>
                <td style="width:300px;">kůň</td>
                <td style="width:150px;">stáj</td>
            </tr>
            @foreach($entry->registrations as $p)
                <tr>
                    <td style="text-align:center;">{{ $p->start_cislo }}</td>
                    <td>{{ $p->osoba?->prijmeni }} {{ $p->osoba?->jmeno }}</td>
                    <td>{{ $p->kun?->jmeno }} ({{ $p->kun?->pohlavi }})</td>
                    <td>{{ $p->osoba?->staj }}</td>
                </tr>
            @endforeach
        </table>
    @endforeach
@endforeach
</body>
</html>
