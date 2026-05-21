<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Registration Confirmation</title>
</head>
<body style="margin:0; padding:0; background-color:#f4f6f8; font-family: Arial, sans-serif;">
    <br><br>
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f4f6f8; padding:30px 0;">

        <tr>
            <td align="center">
                
                <table width="600" cellpadding="0" cellspacing="0" border="0" style="background-color:#ffffff; border-radius:12px; overflow:hidden; box-shadow:0 4px 15px rgba(0,0,0,0.05);">
                    
                    <!-- Header -->
                    <tr>
                        <td style="background-color:#64c727; padding:25px; text-align:center;">
                            <h1 style="color:#ffffff; margin:0; font-size:22px; letter-spacing:0.5px;">
                                REGISTRATION CONFIRMATION
                            </h1>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:25px; text-align:center;">
                            <img src="https://my.cughlima2027.org/assets/img/logo2-mail.png" alt="Logo CUGH" style="width: 120px; max-width: 120px; height: auto; display: block; margin: 0 auto;">
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding:30px;">
                            
                            <p style="font-size:15px; color:#333333; line-height:1.6;">
                                We are pleased to confirm your registration at <strong>CUGH´s 18<sup>th</sup> Annual Conference</strong>, to be held at the Swissotel Lima – Peru, February 25 – 28, 2027.
                            </p>

                            <table width="100%" cellpadding="8" cellspacing="0" border="0" style="margin-top:20px; font-size:14px;">
                                <tr style="background-color:#f9f9f9;">
                                    <td><strong>Participant’s Name</strong></td>
                                    <td>{{ $userinfo->name }} {{ $userinfo->lastname }} {{ $userinfo->second_lastname }}</td>
                                </tr>
                                <tr>
                                    <td><strong>ID Number</strong></td>
                                    <td>{{ $datainscription->id }}</td>
                                </tr>
                                <tr style="background-color:#f9f9f9;">
                                    <td><strong>Country</strong></td>
                                    <td>{{ $userinfo->country_name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Category</strong></td>
                                    <td>{{ $datainscription->category_inscription_name }}</td>
                                </tr>
                                <tr style="background-color:#f9f9f9;">
                                    <td><strong>Registration fee</strong></td>
                                    <td><strong>US$ {{ $datainscription->total }}</strong></td>
                                </tr>
                                <tr>
                                    @php 
                                        $card_number = '';

                                        if (!empty($datainscription->payment_card_number)) {

                                            $last4 = substr($datainscription->payment_card_number, -4);

                                            $card_number = ' <small style="color:#5e5e5e;">(****' . $last4 . ')</small>';
                                        }
                                    @endphp

                                    <td><strong>Payment Method</strong></td>
                                    <td>{!! $datainscription->payment_method . $card_number !!}</td>
                                </tr>
                                <tr style="background-color:#f9f9f9;">
                                    <td><strong>Status</strong></td>
                                    <td>{{ $datainscription->status }}</td>
                                </tr>
                            </table>

                            <p style="margin-top:25px; font-size:15px; color:#333;">
                                Should you need any additional information, please reach out at the e-mail address mentioned below; we will be delighted to assist you.
                            </p>

                            <p style="margin-top:25px; font-size:15px; color:#333;">
                                Thank you for participating at <strong>CUGH 2027 – Lima, Peru</strong>.
                            </p>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color:#f9f9f9; padding:20px; font-size:13px; color:#666; text-align:center;">
                            <strong style="color:#CC1F2F;">CUGH 2027 Registration Office</strong><br>
                            Ms. Millie Estrada<br>
                            Email: 
                            <a href="mailto:registration@cughlima2027.org" style="color:#CC1F2F; text-decoration:none;">
                                registration@cughlima2027.org
                            </a><br>
                            WhatsApp: +51 983 481 269<br>
                            <span style="color:#CC1F2F; text-decoration:none;">www.cughlima2027.org</span>
                            <br><br>
                        </td>
                    </tr>

                </table>

            </td>
        </tr>

        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" border="0" style="background-color:#ffffff; border-radius:12px; overflow:hidden; box-shadow:0 4px 15px rgba(0,0,0,0.05);">
                    <tr>
                        <td style="padding:25px; text-align:center;">
                            <p style="text-align: center;"><strong>Conference Venue:</strong></p>
                            <p style="text-align: center;"><strong>Swissôtel Lima</strong><br>Av. Santo Toribio 173 – Vía Central 150<br>Centro Empresarial Real<br>San Isidro, Lima, Peru</p>
                            <br>
                            <p style="text-align: center;"><strong style="color:blue;">Registration opens Friday, February 24th, 2027, 3:00 pm</strong></p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

    </table>

</body>
</html>