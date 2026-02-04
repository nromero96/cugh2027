<!DOCTYPE html>
<html>
<head>
    <title>Nuevo trabajo registrado</title>
</head>
<body>
    <h3>Nuevo trabajo registrado</h3>
    <p>Se ha registrado un nuevo trabajo con el título "{{ $work->title }}".</p>
    <p>Para ver el trabajo, haz click en el siguiente enlace: <a href="{{ route('works.show', $work->id) }}">{{ route('works.show', $work->id) }}</a></p>
    <p>Si necesita más información o asistencia adicional, no dude en ponerse en contacto con nosotros.</p><br>
    <p>Atentamente,<br>Comité Organizador<br><b>CUGH 2027</b><br>+51 983 481 269<br>info@cughlima2027.org</p>
</body>
</html>

