<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><title>Panel reviewer account</title></head>
<body style="margin:0;padding:0;background:#f4f6f9;font-family:Arial,Helvetica,sans-serif;color:#243047;">
<table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="padding:24px 12px;background:#f4f6f9;"><tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" role="presentation" style="width:100%;max-width:600px;background:#ffffff;border:1px solid #e4e9f0;">
<tr><td style="padding:24px 30px;background:#CC1F2F;color:#ffffff;"><h1 style="margin:0;font-size:22px;color:#ffffff;">Panel Reviewer Account</h1><p style="margin:6px 0 0;font-size:13px;">18th CUGH Annual Conference · Lima, Peru</p></td></tr>
<tr><td style="padding:28px 30px;font-size:15px;line-height:1.65;">
<p style="margin:0 0 18px;">Dear {{ trim($user->name.' '.$user->lastname) ?: 'Reviewer' }},</p>
<p style="margin:0 0 18px;">Thank you for agreeing to review panel submissions for the 18th CUGH Annual Conference in Lima, Peru, February 25–28, 2027.</p>
<p style="margin:0 0 18px;">We have created your account for the conference review platform. Use these credentials to sign in and view any panels assigned to you:</p>
<table width="100%" cellpadding="10" cellspacing="0" role="presentation" style="margin:20px 0;background:#f8fafc;border:1px solid #e4e9f0;"><tr><td width="110"><strong>Email</strong></td><td>{{ $user->email }}</td></tr><tr><td><strong>Password</strong></td><td style="font-family:Courier New,monospace;font-size:17px;"><strong>{{ $plainPassword }}</strong></td></tr></table>
<p style="text-align:center;margin:26px 0;"><a href="{{ route('login') }}" style="display:inline-block;padding:12px 22px;background:#CC1F2F;color:#ffffff;text-decoration:none;">Sign in to your account</a></p>
<p style="margin:0;color:#5c6779;font-size:13px;">Please keep these credentials private. If you already have an account, continue using your existing password; the “Forgot Password?” option is available on the sign-in page.</p>
</td></tr><tr><td style="padding:16px 30px;background:#CC1F2F;color:#ffffff;font-size:12px;text-align:center;">CUGH Lima 2027 · Panel Review</td></tr>
</table></td></tr></table></body></html>
