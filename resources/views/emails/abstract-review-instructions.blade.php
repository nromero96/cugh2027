<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Abstract Review Instructions</title>
</head>
<body style="margin:0;padding:0;background:#f4f6f9;font-family:Arial,Helvetica,sans-serif;color:#243047;">
    @php
        $guideImage = !empty($preview)
            ? asset('assets/img/abstract-review-guide.jpg')
            : $message->embed(public_path('assets/img/abstract-review-guide.jpg'));
    @endphp
    <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="padding:24px 12px;background:#f4f6f9;">
        <tr>
            <td align="center">
                <table width="640" cellpadding="0" cellspacing="0" role="presentation" style="width:100%;max-width:640px;background:#ffffff;border:1px solid #e4e9f0;">
                    <tr>
                        <td style="padding:24px 30px;background:#CC1F2F;color:#ffffff;">
                            <h1 style="margin:0;font-size:22px;line-height:1.3;color:#ffffff;">Abstract Review Instructions</h1>
                            <p style="margin:6px 0 0;font-size:13px;color:#ffffff;">18th CUGH Annual Conference · Lima, Peru</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px 30px;font-size:15px;line-height:1.65;">
                            <p style="margin:0 0 18px;">Dear CUGH Education Committee member:</p>
                            <p style="margin:0 0 18px;">
                                We would like to thank you for accepting to serve as a reviewer of the abstracts submitted to the 18th CUGH Annual Conference, which will take place in Lima, Peru, from February 25 to 28, 2027, under the theme <strong><em>Transforming Global Health: Partnerships, Power, Leadership, and Technology in a Rapidly Changing World</em></strong>.
                            </p>
                            <p style="margin:0 0 14px;">Please find below the instructions for scoring the GLOBAL HEALTH EDUCATION abstracts:</p>

                            <ol style="margin:0 0 20px;padding-left:24px;">
                                <li style="padding-left:4px;margin-bottom:14px;">
                                    To access the conference abstract review platform, please visit <a href="https://my.cughlima2027.org/" style="color:#CC1F2F;">my.cughlima2027.org</a> and register using the e-mail address at which you received this message. Enter your password to access the event's scoring system. If you have previously registered as a user, you can access the platform directly. In case you do not remember your password you may retrieve it by clicking on Forgot Password.
                                </li>
                                <li style="padding-left:4px;margin-bottom:14px;">
                                    The system will display the abstracts assigned to you for review, based on the track you indicated in the survey previously sent by the CUGH. We are very grateful for your effort and dedication in evaluating the number of abstracts assigned to each SPAC member.
                                </li>
                                <li style="padding-left:4px;margin-bottom:14px;">
                                    The entire scoring process takes place directly on the platform. You will be able to view the following details in your personal dashboard:
                                    <ol type="a" style="margin:7px 0 0;padding-left:22px;">
                                        <li>Abstract number</li>
                                        <li>Abstract title</li>
                                        <li>Abstract type: Scientific or Program &amp; Project</li>
                                    </ol>
                                </li>
                                <li style="padding-left:4px;margin-bottom:14px;">To process the review, open each abstract to view the content exactly as submitted by the main author.</li>
                                <li style="padding-left:4px;margin-bottom:14px;">
                                    The established criteria include five items:
                                    <ol type="a" style="margin:7px 0 0;padding-left:22px;">
                                        <li>Structure of the abstract</li>
                                        <li>Clarity of the writing</li>
                                        <li>Degree of innovation of the program/initiative</li>
                                        <li>Does the abstract address an important global health challenge (human, environmental, social, political, etc.)</li>
                                        <li>The degree to which solutions/recommendations proposed could impact policy or a global health challenge</li>
                                    </ol>
                                </li>
                                <li style="padding-left:4px;margin-bottom:14px;">
                                    You will then evaluate each of the 5 established criteria on a scale of 1 to 10; the maximum possible score for each criterion (maximum 10) is displayed next to your score. The scale appears on the system: Poor [1 to 3]; Fair [4 to 6]; Good [7 to 8]; Excellent [9 to 10].
                                    <img src="{{ $guideImage }}" alt="Screenshot of the abstract evaluation form and scoring guide" width="540" style="display:block;width:100%;max-width:540px;height:auto;margin:14px 0 4px;border:1px solid #e4e9f0;">
                                </li>
                                <li style="padding-left:4px;margin-bottom:14px;">Upon completing your evaluation, the system will display the total score for the abstract, along with the corresponding average.</li>
                                <li style="padding-left:4px;margin-bottom:14px;">Finally, click on the red box <strong>SAVE EVALUATION</strong>.</li>
                                <li style="padding-left:4px;margin-bottom:14px;">Please note that you <strong>cannot</strong> re-score an abstract once you have submitted (saved) your final score. We look forward to receiving your evaluation no later than October 5 at 11:59 PM (Lima local time).</li>
                            </ol>

                            <p style="margin:0 0 15px;">We appreciate your kind cooperation and support in the arduous task of grading the submissions.</p>
                            <p style="margin:0 0 18px;">Should you have any questions regarding the review and evaluation process, please contact <a href="mailto:secretariat@cughlima2027.org" style="color:#CC1F2F;">secretariat@cughlima2027.org</a>.</p>
                            <p style="margin:0;">With all kind regards,<br><strong>Patricia Garcia, MD, MPH, PhD</strong><br>Chair, CUGH Lima 2027 Annual Conference</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:16px 30px;background:#CC1F2F;color:#ffffff;font-size:12px;text-align:center;">CUGH Lima 2027 · Abstract Review</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
