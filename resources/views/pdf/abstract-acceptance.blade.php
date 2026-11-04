<table cellpadding="0" cellspacing="0" width="100%">
    <tr><td align="right">Lima, {{ $letterDate }}</td></tr>
</table>

<br>
@if($attentionName !== '')
<p><strong>Attention: {{ $attentionName }}</strong></p>
@endif

<br>
<p>Dear Colleague,</p>

<p>On behalf of the organizing committee of the 18<sup>th</sup> Annual Conference of the Consortium of Universities for Global Health (CUGH Lima 2027), we are pleased to inform you that your submitted abstract, titled:</p>

<br>
<p><strong>{{ $abstractTitle }}</strong></p>
<br>

<p>has been accepted for <strong>{{ $presentationLabel }}</strong> presentation during the conference, which will take place at the Swissôtel Lima from February 25 to 28, 2027. In this regard, please note the following:</p>

<p>1. <strong>Acknowledgment and confirmation:</strong> We kindly request that you acknowledge receipt of this message and reconfirm your intention to present the aforementioned abstract.</p>

<p>2. <strong>Registration:</strong> In accordance with CUGH guidelines, all abstract presenters must be duly registered for the conference. The registration deadline is <strong style="color:#CC1F2F;">November 30.</strong> Please note that registration is handled online, and the preferential (early-bird) registration rate remains in effect until that date. To register, please visit: <a href="https://my.cughlima2027.org/" style="color:#CC1F2F;text-decoration:underline;">https://my.cughlima2027.org/</a></p>

<p>3. <strong>Accommodation:</strong> Participants may book accommodation at the Swissôtel or Novotel (venues 1 and 2, respectively). The organizers have also secured preferential rates at other nearby hotels, which include a buffet breakfast and Wi-Fi access. For reservations, please contact: <a href="mailto:housing@cughlima2027.org" style="color:#CC1F2F;text-decoration:underline;">housing@cughlima2027.org</a>.</p>

<p>4. <strong>Entry Visas for Peru:</strong> Please remind to verify, well in advance, whether you require a visa to enter Peru. Citizens of certain countries are subject to this requirement. Please note that Peru does not issue visas upon arrival at Jorge Chávez International Airport, nor does it offer electronic visas (e-visas). All visa arrangements must be made through a Peruvian consulate abroad. For further information, please visit <a href="https://cughlima2027.org/visa-to-enter-peru" style="color:#CC1F2F;text-decoration:underline;">https://cughlima2027.org/visa-to-enter-peru</a>.</p>

@if($presentationType === 'Oral Presentation')
<p>5. <strong>Agenda/Program:</strong> Upon complete receipt of confirmed ORAL presentations, we will schedule your participation according to the general program structured by the organizers. Oral presentations will take place Friday, February 26, through Sunday, February 28, 2027. Specific date and time for your session, including room assignment and the final schedule, will be announced starting December 10. <span style="color:#CC1F2F;">The organizers will disregard all accepted abstracts in case registration is not confirmed by <strong>November 30, 2026</strong>.</span></p>
@else
<p>5. <strong>Agenda/Program:</strong> Upon complete receipt of confirmed POSTER presentations, we will schedule your exhibit according to the general program structured by the organizers. Poster presentations will take place Thursday, February 25, through Saturday, February 27, 2027. Specific date and time for your poster presentation will be announced starting December 10. <span style="color:#CC1F2F;">The organizers will disregard all accepted abstracts to be exhibited as POSTERS in case registration is not confirmed by <strong>November 30, 2026</strong>.</span></p>
@endif

<p>We are very pleased to having you as presenter at the CUGH LIMA 2027 Annual Conference, and we thank you in advance for your participation.</p>

<p>With all kind regards,</p>
<p><strong>Secretariat – Organizing Committee</strong><br>
18th Annual Conference<br>
CUGH Lima 2027<br>
+51 998672199<br>
<a href="mailto:secretariat@cughlima2027.org" style="color:#CC1F2F;text-decoration:underline;">secretariat@cughlima2027.org</a></p>
