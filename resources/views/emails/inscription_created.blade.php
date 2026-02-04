<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            background-color: #f5f5f5;
        }

        h2 {
            color: #000000;
        }

        h3 {
            color: #000000;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #dbdbdb;
            color: #000000;
        }

        td {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
        }

        a {
            color: #007bff;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .signature {
            margin-top: 20px;
            font-size: 14px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>REGISTRATION # {{ $datainscription->id }}: En Proceso</h2>
        <p>Dear <b>{{ $userinfo->name }} {{ $userinfo->lastname }} {{ $userinfo->second_lastname }}</b>,</p>
        <p>Le informamos que su preinscripción para la <b>AAAAAAAAAAAAA</b> ha sido recibida. El evento se llevará a cabo del 8 al 11 de mayo de 2026 en el Swissôtel Lima.</p>

        <!-- Título "Detalle de tu Inscripción" -->
        <h3>Details of your registration</h3>

        <!-- Tabla de resumen de inscripción con bordes -->
        <table>
            <tr>
                <th>Descripción</th>
                <th>Información</th>
            </tr>
            <tr>
                <td>Nombre Completo</td>
                <td>{{ $userinfo->name }} {{ $userinfo->lastname }} {{ $userinfo->second_lastname }}</td>
            </tr>
            <tr>
                <td>Categoría</td>
                <td>{{ $datainscription->category_inscription_name }}</td>
            </tr>
            <tr>
                <td>Precio</td>
                <td>US$ {{ $datainscription->price_category }}</td>
            </tr>
            <tr>
                <td>Acompañante</td>
                <td>US$ {{ $datainscription->price_accompanist }}</td>
            </tr>
            <tr>
                <td><b>Monto Total</b></td>
                <td>US$ {{ $datainscription->total }}</td>
            </tr>
            <tr>
                <td>Método de Pago</td>
                <td>{{ $datainscription->payment_method }}</td>
            </tr>
        </table>
        <!-- Fin de la tabla -->

        <!-- Recordatorio para ver el proceso de inscripción -->
        <p>Recuerda que puedes ver el proceso de tu inscripción en el siguiente enlace:</p>
        <p><a href="#">Ver Proceso de Inscripción</a></p>

        <!-- Mensaje de validación de pago e información -->
        <p>Antes de aprobar su inscripción, validaremos el pago y la información proporcionada.</p>

        <!-- Contacto de soporte -->
        <p>Estamos aquí para ayudarle en caso de cualquier consulta o aclaración adicional. Para mayores detalles, puede contactarse con nosotros a través del e-mail <b>inscripciones@dominioevento.org</b></p><br>

        <!-- Firma y contacto del Comité Organizador -->
        <p class="signature">Atentamente,<br>Comité Organizador<br><b>CUGH 2027 Registration Office</b><br>+51 983 481 269<br>inscripciones@dominioevento.org</p>
    </div>
</body>
</html>