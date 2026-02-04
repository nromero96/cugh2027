<!DOCTYPE html>
<html>
<head>
    <title>Trabajo Aceptado</title>
</head>
<body>

    <p style="text-align: center;">
        <img src="https://my.cughlima2027.org/assets/img/logo2.png" alt="CUGH 2027" style="width: 135px;">
    </p>

    <h1 style="color: #c00000;font-size: 22px;text-align: center;line-height: 14px;font-family: arial;">
        XLI REUNIÓN ANUAL DE DERMATÓLOGOS LATINOAMERICANOS
        <span style="display: block; font-size: 19px; margin-top: 10px; color:black;font-weight: normal;">Swissôtel Lima, 8 al 11 de Mayo de 2024</span>
    </h1>
    <div style="color: rgba(255, 255, 255, 0); height: 20px;">-</div>
    <p style="font-size: 14px;">
        Lima, 21 de febrero de 2024
    </p>
    <div style="color: rgba(255, 255, 255, 0); height: 1px;">-</div>
    <p style="font-size: 14px;">
        Estimado(a) Dr.(a) <strong>{{ $userName }} {{ $userLastName }} {{ $userSecondLastName }}</strong>
    </p>

    <p style="font-size: 14px;">
        Por medio del presente tenemos el agrado de confirmarle que su(s) trabajo(s) presentado(s) a CUGH 2027 han sido aceptados para ser presentados en poster electrónico.
    </p>

    <table style="width:100%;text-align: left; font-size: 14px;">
        <thead>
            <tr>
                <th style="border: 1px solid #d4d4d4;background: #dbdbdb;"><b>N°</b></th>
                <th style="border: 1px solid #d4d4d4;background: #dbdbdb;"><b>CATEGORÍA</b></th>
                <th style="border: 1px solid #d4d4d4;background: #dbdbdb;"><b>TITULO DEL TRABAJO</b></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="border: 1px solid #d4d4d4;">{{ $work->id }}</td>
                <td style="border: 1px solid #d4d4d4;">{{ $work->category }}</td>
                <td style="border: 1px solid #d4d4d4;">{{ $work->title }}</td>
            </tr>
        </tbody>
    </table>

    <p style="font-size: 14px;">
        Le invitamos cordialmente a inscribirse en el evento si a la fecha de envío de este mensaje aún no lo ha podido concretar.  De acuerdo a las bases de presentación de trabajos, es requisito indispensable estar inscritos en CUGH 2027.
    </p>

    <p style="font-size: 14px;">
        El plazo para confirmar su inscripción es el 28 de febrero.  Ingresar a www.WEB.org pestaña superior derecha INSCRIBIRME.
    </p>

    <p style="font-size: 14px;">
        Estamos a su disposición para cualquier consulta sobre el particular.
    </p>

    <p style="font-size: 14px;">Atentos saludos,</p>

    <p style="font-weight: bold; font-size: 14px; margin-bottom: 0px;">
        Comité Organizador<br>
        <img src="https://my.cughlima2027.org/assets/img/logo-rosmar.png" alt="ROSMAR" style="width: 120px;">
    </p>

    <p style="font-size: 12px; margin-top: 0px;">
        <b>Rosa Sheen</b><br>
        Directora<br>
        Tel.  <u>(51 1) 4778694</u><br>
        WhatsApp <u>+51 998672199</u><br>
        E-mail:  <u>rosmarasoc@rosmarasociados.com</u><br>
        <u>www.CUGH2027.org</u><br>
        <img src="https://my.cughlima2027.org/assets/img/logo2.png" alt="CUGH 2027" style="width: 80px;">
    </p>

</body>
</html>