<html>
<head><meta charset="utf-8">@include('exports._styles')</head>
<body>
<table border="1" cellspacing="0" cellpadding="4">
    <tr><td>ID Přihlášky</td><td>Email</td><td>Příjmení Jméno</td></tr>
    @foreach($prihlasky as $p)
        <tr>
            <td>{{ $p->id }}</td>
            <td>{{ $p->user?->email }}</td>
            <td>{{ $p->osoba?->prijmeni }} {{ $p->osoba?->jmeno }}</td>
        </tr>
    @endforeach
</table>
</body>
</html>
