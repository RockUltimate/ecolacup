<html>
<head><meta charset="utf-8">@include('exports._styles')</head>
<body>
<h1>{{ $udalost->nazev }}</h1>
<h3>{{ $udalost->datum_zacatek?->format('d.m.Y') }} - {{ $udalost->misto }}</h3>
<table border="1" cellspacing="10" cellpadding="10">
    <tr>
        <td>Jméno koně</td>
        <td>Majitel koně</td>
        <td>číslo průkazu koně</td>
        <td>číslo hosp.</td>
        <td>Pozn.</td>
    </tr>
    @foreach($kone as $k)
        <tr>
            <td>{{ $k->jmeno }}</td>
            <td>{{ $k->majitel_jmeno_adresa }}</td>
            <td>{{ $k->cislo_prukazu }}</td>
            <td>{{ $k->cislo_hospodarstvi }}</td>
            <td>&nbsp;</td>
        </tr>
    @endforeach
</table>
</body>
</html>
