<html>
<head><meta charset="utf-8">@include('exports._styles')</head>
<body>
<h2>{{ $udalost->nazev }}</h2>
<table cellspacing="0" cellpadding="5" border="1" width="500">
    @foreach($pocty as $nazev => $count)
        <tr>
            <td>{{ $nazev }}</td>
            <td>{{ $count }}</td>
        </tr>
    @endforeach
</table>
<h2>Celkem startů: {{ $totalStartu }}</h2>
</body>
</html>
