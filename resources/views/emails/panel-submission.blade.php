<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Panel Submission</title>
</head>
<body style="margin:0;padding:0;background:#f4f6f9;font-family:Arial,Helvetica,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6f9;padding:30px 15px;">
    <tr>
        <td align="center">

            <!-- MAIN CONTAINER -->
            <table width="750" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:14px;overflow:hidden;box-shadow:0 4px 15px rgba(0,0,0,0.08);">

                <!-- HEADER -->
                <tr>
                    <td style="background:#111827;padding:40px 30px;text-align:center;">

                        <h1 style="color:#ffffff;margin:0;font-size:30px;font-weight:bold;">
                            Panel Submission
                        </h1>

                        <p style="color:#d1d5db;margin-top:10px;font-size:15px;">
                            CUGH Lima Peru 2027
                        </p>

                    </td>
                </tr>

                <!-- BODY -->
                <tr>
                    <td style="padding:35px;">

                        <!-- PANEL DETAILS -->
                        <div style="margin-bottom:35px;">

                            <h2 style="margin:0 0 20px;color:#111827;font-size:22px;border-left:5px solid #ec008c;padding-left:12px;">
                                Panel Details
                            </h2>

                            <table width="100%" cellpadding="8" cellspacing="0">

                                <tr>
                                    <td width="180">
                                        <strong>Language</strong>
                                    </td>
                                    <td>
                                        {{ $panel->language }}
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <strong>Title</strong>
                                    </td>
                                    <td>
                                        {{ $panel->title }}
                                    </td>
                                </tr>

                                <tr>
                                    <td valign="top">
                                        <strong>Sub-Themes</strong>
                                    </td>
                                    <td>

                                        @if($panel->subthemes)

                                            @foreach($panel->subthemes as $subtheme)

                                                <span style="display:inline-block;background:#ec008c;color:#fff;padding:6px 12px;border-radius:30px;font-size:12px;margin:3px;">
                                                    {{ $subtheme }}
                                                </span>

                                            @endforeach

                                        @endif

                                        @if($panel->subthemes_other)

                                            <div style="margin-top:10px;">
                                                <strong>Other:</strong>
                                                {{ $panel->subthemes_other }}
                                            </div>

                                        @endif

                                    </td>
                                </tr>

                            </table>

                        </div>

                        <!-- CONTACT -->
                        <div style="margin-bottom:35px;">

                            <h2 style="margin:0 0 20px;color:#111827;font-size:22px;border-left:5px solid #66cc00;padding-left:12px;">
                                Contact Person
                            </h2>

                            <table width="100%" cellpadding="8" cellspacing="0">

                                <tr>
                                    <td width="180"><strong>Name</strong></td>
                                    <td>{{ $panel->contact_salutation }} {{ $panel->contact_name }}</td>
                                </tr>

                                <tr>
                                    <td><strong>Institution</strong></td>
                                    <td>{{ $panel->contact_institution }}</td>
                                </tr>

                                <tr>
                                    <td><strong>Country</strong></td>
                                    <td>{{ $panel->contact_country }}</td>
                                </tr>

                                <tr>
                                    <td><strong>Phone</strong></td>
                                    <td>{{ $panel->contact_phone }}</td>
                                </tr>

                                <tr>
                                    <td><strong>Email</strong></td>
                                    <td>
                                        <a href="mailto:{{ $panel->contact_email }}" style="color:#2563eb;text-decoration:none;">
                                            {{ $panel->contact_email }}
                                        </a>
                                    </td>
                                </tr>

                            </table>

                        </div>

                        <!-- MODERATOR -->
                        <div style="margin-bottom:35px;">

                            <h2 style="margin:0 0 20px;color:#111827;font-size:22px;border-left:5px solid #ff9800;padding-left:12px;">
                                Moderator
                            </h2>

                            <table width="100%" cellpadding="8" cellspacing="0">

                                <tr>
                                    <td width="180"><strong>Name</strong></td>
                                    <td>{{ $panel->moderator_name }}</td>
                                </tr>

                                <tr>
                                    <td><strong>Position</strong></td>
                                    <td>{{ $panel->moderator_position }}</td>
                                </tr>

                                <tr>
                                    <td><strong>Institution</strong></td>
                                    <td>{{ $panel->moderator_institution }}</td>
                                </tr>

                                <tr>
                                    <td><strong>Country</strong></td>
                                    <td>{{ $panel->moderator_country }}</td>
                                </tr>

                            </table>

                        </div>

                        <!-- SPEAKERS -->
                        <div style="margin-bottom:35px;">

                            <h2 style="margin:0 0 20px;color:#111827;font-size:22px;border-left:5px solid #ffee00;padding-left:12px;">
                                Speakers
                            </h2>

                            @if($panel->speakers)

                                @foreach($panel->speakers as $speaker)

                                    <div style="border:1px solid #e5e7eb;border-radius:12px;padding:20px;margin-bottom:18px;background:#fafafa;">

                                        <table width="100%" cellpadding="6">

                                            <tr>
                                                <td width="160"><strong>Name</strong></td>
                                                <td>{{ $speaker['name'] ?? '' }}</td>
                                            </tr>

                                            <tr>
                                                <td><strong>Position</strong></td>
                                                <td>{{ $speaker['position'] ?? '' }}</td>
                                            </tr>

                                            <tr>
                                                <td><strong>Institution</strong></td>
                                                <td>{{ $speaker['institution'] ?? '' }}</td>
                                            </tr>

                                            <tr>
                                                <td><strong>Country</strong></td>
                                                <td>{{ $speaker['country'] ?? '' }}</td>
                                            </tr>

                                        </table>

                                    </div>

                                @endforeach

                            @else

                                <p>No speakers added.</p>

                            @endif

                        </div>

                        <!-- DESCRIPTION -->
                        <div style="margin-bottom:35px;">

                            <h2 style="margin:0 0 20px;color:#111827;font-size:22px;border-left:5px solid #2563eb;padding-left:12px;">
                                Panel Description
                            </h2>

                            <div style="background:#f9fafb;border-radius:12px;padding:20px;border:1px solid #e5e7eb;line-height:1.8;">
                                {!! nl2br(e($panel->description)) !!}
                            </div>

                        </div>

                        <!-- LEARNING -->
                        <div style="margin-bottom:10px;">

                            <h2 style="margin:0 0 20px;color:#111827;font-size:22px;border-left:5px solid #7c3aed;padding-left:12px;">
                                Learning Objectives
                            </h2>

                            <div style="background:#f9fafb;border-radius:12px;padding:20px;border:1px solid #e5e7eb;line-height:1.8;">
                                {!! nl2br(e($panel->learning_objectives)) !!}
                            </div>

                        </div>

                    </td>
                </tr>

                <!-- FOOTER -->
                <tr>
                    <td style="background:#111827;padding:25px;text-align:center;">

                        <p style="margin:0;color:#d1d5db;font-size:13px;">
                            © 2027 CUGH Lima Peru. All rights reserved.
                        </p>

                    </td>
                </tr>

            </table>

        </td>
    </tr>
</table>

</body>
</html>