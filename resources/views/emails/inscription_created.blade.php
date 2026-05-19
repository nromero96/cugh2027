<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Registration Created</title>
</head>
<body style="margin:0; padding:0; background-color:#f4f6f8; font-family: Arial, sans-serif;">
    <br><br>
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f4f6f8; padding:30px 0;">
        <tr>
            <td align="center">
                
                <table width="600" cellpadding="0" cellspacing="0" border="0" style="background-color:#ffffff; border-radius:12px; overflow:hidden; box-shadow:0 4px 15px rgba(0,0,0,0.05);">
                    
                    <!-- Header -->
                    <tr>
                        <td style="background-color:#CC1F2F; padding:25px; text-align:center;">
                            <h1 style="color:#ffffff; margin:0; font-size:22px; letter-spacing:0.5px;">
                                REGISTRATION # {{ $datainscription->id }}
                            </h1>
                            <p style="color:#F2A413; margin:8px 0 0 0; font-size:15px; font-weight:bold;">
                                IN PROCESS
                            </p>
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
                                This is to confirm your <strong>pre-registration</strong> at 
                                <strong>CUGH’s 18th Annual Conference</strong>, 
                                to be held in <strong>Lima – Peru</strong>, 
                                February 25–28, 2027.
                            </p>

                            <table width="100%" cellpadding="8" cellspacing="0" border="0" style="margin-top:20px; font-size:14px;">
                                <tr style="background-color:#f9f9f9;">
                                    <td><strong>Participant’s Name</strong></td>
                                    <td>{{ $userinfo->name }} {{ $userinfo->lastname }} {{ $userinfo->second_lastname }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Country</strong></td>
                                    <td>{{ $userinfo->country_name }}</td>
                                </tr>
                                <tr style="background-color:#f9f9f9;">
                                    <td><strong>Category</strong></td>
                                    <td>{{ $datainscription->category_inscription_name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Paid Amount</strong></td>
                                    <td><strong>US$ {{ $datainscription->total }}</strong></td>
                                </tr>
                                <tr style="background-color:#f9f9f9;">
                                    <td><strong>Payment Method</strong></td>
                                    <td>{{ $datainscription->payment_method }}</td>
                                </tr>
                            </table>

                            <!-- Important Notice -->
                            <div style="margin-top:25px; padding:15px; border-left:4px solid #F2A413; background-color:#fff8e6;">
                                <p style="margin:0; font-size:13px; color:#555;">
                                    <strong style="color:#CC1F2F;">Important:</strong> 
                                    Final confirmation will be sent once payment verification is completed. 
                                    Invoicing may take up to <strong>4 working days</strong>.
                                </p>
                            </div>

                            <p style="margin-top:25px; font-size:15px; color:#333;">
                                Thank you for participating in <strong>CUGH 2027 – Lima, Peru</strong>.
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
                            WhatsApp: +51 983 481 269
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>
</html>