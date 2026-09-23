<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your CUGH 2027 reviewer account</title>
</head>
<body style="margin:0;padding:0;background:#f4f6f9;font-family:Arial,Helvetica,sans-serif;color:#1f2937;">
    <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="padding:32px 15px;background:#f4f6f9;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" role="presentation" style="width:100%;max-width:600px;background:#ffffff;border-radius:14px;overflow:hidden;box-shadow:0 4px 15px rgba(0,0,0,.08);">
                    <tr>
                        <td style="padding:30px;background:#CC1F2F;text-align:center;">
                            <h1 style="margin:0;color:#ffffff;font-size:25px;">CUGH 2027 Reviewer Account</h1>
                            <p style="margin:8px 0 0;color:#d1d5db;">Abstract evaluation platform</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:32px;">
                            <p style="margin-top:0;font-size:16px;line-height:1.6;">
                                Hello {{ trim($user->name.' '.$user->lastname) ?: 'Reviewer' }},
                            </p>
                            <p style="font-size:15px;line-height:1.7;">
                                We would like to thank you for accepting to serve as a reviewer of the abstracts submitted to the 18th CUGH Annual Conference, which will take place in Lima, Peru, from February 25 to 28, 2027, under the theme <strong><em>Transforming Global Health: Partnerships, Power, Leadership, and Technology in a Rapidly Changing World.</em></strong>
                            </p>
                            <p style="font-size:15px;line-height:1.7;">
                                An account has been created for you on the CUGH 2027 platform. Use the credentials below to sign in and access any abstracts assigned to you.
                            </p>

                            <table width="100%" cellpadding="10" cellspacing="0" role="presentation" style="margin:24px 0;background:#f8fafc;border:1px solid #e5e7eb;border-radius:10px;">
                                <tr>
                                    <td style="width:110px;color:#6b7280;"><strong>Email</strong></td>
                                    <td style="word-break:break-all;"><strong>{{ $user->email }}</strong></td>
                                </tr>
                                <tr>
                                    <td style="color:#6b7280;"><strong>Password</strong></td>
                                    <td><strong style="font-family:Courier New,monospace;font-size:17px;letter-spacing:1px;">{{ $plainPassword }}</strong></td>
                                </tr>
                            </table>

                            <p style="margin:28px 0;text-align:center;">
                                <a href="{{ route('login') }}" style="display:inline-block;padding:13px 24px;background:#CC1F2F;color:#ffffff;text-decoration:none;border-radius:7px;font-weight:bold;">Sign in to your account</a>
                            </p>

                            <p style="margin-bottom:0;color:#6b7280;font-size:13px;line-height:1.6;">
                                Please keep these credentials private. You can use the “Forgot Password?” option on the sign-in page whenever you need to set a new password.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:20px;background:#CC1F2F;text-align:center;color:#ffffff;font-size:12px;">
                            CUGH 2027 · Lima, Peru
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
