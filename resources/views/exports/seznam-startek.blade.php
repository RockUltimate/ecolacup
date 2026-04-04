<html>
<head><meta charset="utf-8">@include('exports._styles')</head>
<body>
@foreach($moznostiSeDisciplinami as $moz)
    <h2>{{ $moz->nazev }}</h2>
    <table cellspacing="0" cellpadding="5" border="1" width="704">
        <tr>
            <td style="width:42px;text-align:center;">st.č.</td>
            <td style="width:170px;">jméno</td>
            <td style="width:300px;">kůň</td>
            <td style="width:150px;">stáj</td>
        </tr>
        @foreach($moz->registrations as $p)
            <tr>
                <td style="text-align:center;">{{ $p->start_cislo }}</td>
                <td>{{ $p->osoba?->prijmeni }} {{ $p->osoba?->jmeno }}{{ $p->vekKategorie() }}</td>
                <td>
                    {{ $p->kun?->jmeno }} ({{ $p->kun?->plemeno_kod }}, {{ $p->kun?->rok_narozeni }}, {{ $p->kun?->pohlavi }})
                    @if($p->kunTandem)
                        + {{ $p->kunTandem->jmeno }} ({{ $p->kunTandem->plemeno_kod }}, {{ $p->kunTandem->rok_narozeni }}, {{ $p->kunTandem->pohlavi }})
                    @endif
                </td>
                <td>{{ $p->osoba?->staj }}</td>
            </tr>
        @endforeach
        <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
    </table>
@endforeach
</body>
</html>
