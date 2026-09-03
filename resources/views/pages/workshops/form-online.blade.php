<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pre-Conference Workshop</title>
    <link rel="icon" type="image/x-icon" href="{{asset('assets/img/favicon.ico')}}"/>
    <link href="{{asset('layouts/vertical-light-menu/css/light/loader.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('layouts/vertical-light-menu/css/dark/loader.css')}}" rel="stylesheet" type="text/css" />
    <script src="{{asset('layouts/vertical-light-menu/loader.js')}}"></script>
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100;0,9..40,400;0,9..40,500;0,9..40,700;1,9..40,100;1,9..40,400;1,9..40,500;1,9..40,700&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="{{asset('bootstrap/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />

    <link href="{{asset('layouts/vertical-light-menu/css/light/plugins.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/css/light/authentication/auth-cover.css')}}" rel="stylesheet" type="text/css" />
    
    <link href="{{asset('layouts/vertical-light-menu/css/dark/plugins.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/css/dark/authentication/auth-cover.css')}}" rel="stylesheet" type="text/css" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    .section-card { margin-bottom: 1.5rem; }
    .signature-box { border: 1px solid #ccc; height: 150px; width: 100%; }
    .char-count { font-size: 0.9rem; color: #6c757d; }

    .lgs-header{
        gap: 65px;
        display: flex;
        justify-content: center;
    }
    .hlg-1 { width: 80px; height: 75px; }
    .hlg-2 { width: 135px; height: 68px; }
    .hlg-3 { width: 200px; height: 61px; }

    @media (max-width: 768px) {
        .lgs-header{
            gap: 20px;
            display: flex;
            justify-content: center;
        }

        .hlg-1 { width: 60px; height: 55px; }
        .hlg-2 { width: 100px; height: 48px; }
        .hlg-3 { width: 140px; height: 41px; }
    }

  </style>
</head>
<body>
<div class="bg-primary py-2 px-1">
    <div class="row">
        <div class="col-6">
            <a class="btn btn-primary" href="https://cughlima2027.org/pre-conference-workshops/">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
        <div class="col-6 text-end">
            <a class="btn btn-primary" href="https://cughlima2027.org/">
                <i class="bi bi-house"></i> Home
            </a>
        </div>
    </div>
</div>
<div class="container py-5">

   {{-- Logos  --}}
   <div class="d-flex justify-content-center mb-4 lgs-header">
    <img src="https://cughlima2027.org/wp-content/uploads/2023/01/Cuch-Logo_main.png" alt="CUGH Logo" class="hlg-1">
    <img src="https://cughlima2027.org/wp-content/uploads/2026/03/CUGH-lg.png" alt="CUGH Logo" class="hlg-2">
    <img src="https://cughlima2027.org/wp-content/uploads/2026/04/LogoCayetanoFullColor.png" alt="CUGH Logo" class="hlg-3">
  </div>

  {{-- Mensaje éxito --}}
  @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
          {{ session('success') }}

          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
  @endif

  {{-- Errores --}}
  @if ($errors->any())
      <div class="alert alert-danger alert-dismissible fade show" role="alert">

          <strong>Please correct the following errors:</strong>
          <p class="mb-0">Your information has been preserved. Please sign again before submitting.</p>

          <ul class="mb-0 mt-2">
              @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
              @endforeach
          </ul>

          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
  @endif

  <h2 class="mb-0 text-center fw-bold">Pre-Conference Workshop Application</h2>
  <p class="text-center text-muted mb-4">Apply now to organize a workshop on Thursday, February 25ᵗʰ, 2027, at the Swissôtel, Lima, Peru.</p>

  <form id="workshopForm" action="{{ route('workshops.storeworkshop') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="submission_token" value="{{ old('submission_token', $submissionToken) }}">
    <div id="workshopClientErrors" class="alert alert-danger d-none" role="alert"></div>
    <!-- 1. Lead Contact Person -->
    <div class="card section-card">
      <h6 class="card-header bg-primary text-white fw-bold py-3">Lead Contact Person</h6>
      <div class="card-body">
        <div class="mb-3">
          <label class="form-label text-muted mb-0">Name<small class="text-danger">⁽*⁾</small></label>
          <input type="text" class="form-control" name="lead_name" required value="{{ old('lead_name') }}" maxlength="255">
        </div>
        <div class="mb-3">
          <label class="form-label text-muted mb-0">Institution<small class="text-danger">⁽*⁾</small></label>
          <input type="text" class="form-control" name="lead_institution" required value="{{ old('lead_institution') }}" maxlength="255">
        </div>
        <div class="mb-3">
          <label class="form-label text-muted mb-0">Professional Title<small class="text-danger">⁽*⁾</small></label>
          <input type="text" class="form-control" name="lead_title" required value="{{ old('lead_title') }}" maxlength="255">
        </div>
        <div class="mb-3">
          <label class="form-label text-muted mb-0">E-mail<small class="text-danger">⁽*⁾</small></label>
          <input type="email" class="form-control" name="lead_email" required value="{{ old('lead_email') }}" maxlength="255">
        </div>
        <div class="mb-3">
          <label class="form-label text-muted mb-0">Phone number (with country & area code)<small class="text-danger">⁽*⁾</small></label>
          <input type="text" class="form-control" name="lead_phone" required value="{{ old('lead_phone') }}" maxlength="255">
        </div>
        <div class="mb-3">
          <label class="form-label text-muted mb-0">Cell Phone (with country code)<small class="text-danger">⁽*⁾</small></label>
          <input type="text" class="form-control" name="lead_cell" required value="{{ old('lead_cell') }}" maxlength="255">
        </div>
      </div>
    </div>

    <!-- 2. Workshop Program Description -->
    <div class="card section-card">
      <h6 class="card-header bg-primary text-white fw-bold py-3">Workshop Program Description</h6>
      <div class="card-body">
        <div class="mb-3">
          <label class="form-label text-muted mb-0">Workshop Title<small class="text-danger">⁽*⁾</small></label>
          <input type="text" class="form-control" name="workshop_title" required value="{{ old('workshop_title') }}" maxlength="255">
        </div>
        <div class="mb-3">
            <label class="form-label text-muted mb-0">
                Workshop Description (max 200 words)
                <small class="text-danger">⁽*⁾</small>
            </label>

            <textarea 
                class="form-control word-limit" 
                name="workshop_desc"
                rows="4"
                data-max-words="200"
                required
            >{{ old('workshop_desc') }}</textarea>

            <div class="d-flex justify-content-between">
                <small class="text-muted">Maximum 200 words allowed</small>
                <small class="word-count text-muted">0 / 200 words</small>
            </div>
        </div>
        <div class="mb-3">
          <label class="form-label text-muted mb-0">Objectives / Skills<small class="text-danger">⁽*⁾</small></label>
          <textarea class="form-control" name="workshop_objectives" rows="3" maxlength="1000" required>{{ old('workshop_objectives') }}</textarea>
        </div>
        <div class="mb-3">
          <label class="form-label text-muted mb-0">Speakers and Facilitators <small>(optional)</small></label>
          <textarea class="form-control" name="workshop_speakers" rows="3" maxlength="1000">{{ old('workshop_speakers') }}</textarea>
        </div>
      </div>
    </div>

    <!-- 3. Workshop Room Options and Rental Rates -->
    <div class="card section-card">
      <h6 class="card-header bg-primary text-white fw-bold py-3">Workshop Room Options</h6>
      <div class="card-body">
        <div class="mb-3">
          <label class="form-label text-muted mb-0">Preferred Time Slot<small class="text-danger">⁽*⁾</small></label>
          <select class="form-select" name="time_slot" required>
            <option value="">Select</option>
            <option value="Morning, 9am-12pm" {{ old('time_slot') === 'Morning, 9am-12pm' ? 'selected' : '' }}>Morning, 9am-12pm</option>
            <option value="Afternoon, 1pm-4pm" {{ old('time_slot') === 'Afternoon, 1pm-4pm' ? 'selected' : '' }}>Afternoon, 1pm-4pm</option>
            <option value="Full Day, 9am-4pm" {{ old('time_slot') === 'Full Day, 9am-4pm' ? 'selected' : '' }}>Full Day, 9am-4pm</option>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label text-muted mb-0">Half or Full Day<small class="text-danger">⁽*⁾</small></label><br>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="day_length" value="Half Day" id="halfDay" required {{ old('day_length') === 'Half Day' ? 'checked' : '' }}>
            <label class="form-check-label" for="halfDay">Half Day</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="day_length" value="Full Day" id="fullDay" {{ old('day_length') === 'Full Day' ? 'checked' : '' }}>
            <label class="form-check-label" for="fullDay">Full Day</label>
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label text-muted mb-0">Preferred Room Set-up<small class="text-danger">⁽*⁾</small></label><br>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="room_setup" value="theater" id="theater" required {{ old('room_setup') === 'theater' ? 'checked' : '' }}>
            <label class="form-check-label" for="theater">Theater</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="room_setup" value="rounds" id="rounds" {{ old('room_setup') === 'rounds' ? 'checked' : '' }}>
            <label class="form-check-label" for="rounds">Rounds</label>
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label text-muted mb-0">Desired Number of Attendees<small class="text-danger">⁽*⁾</small></label>
          <input type="number" class="form-control" name="attendees" min="1" required value="{{ old('attendees') }}" max="2147483647">
        </div>
        <div class="mb-3">
          <label class="form-label text-muted mb-0">Notes or Comments</label>
          <textarea class="form-control" name="notes" rows="2" maxlength="5000">{{ old('notes') }}</textarea>
        </div>
      </div>
    </div>

    <!-- 4. Contact for Invoice -->
    <div class="card section-card">
      <h6 class="card-header bg-primary text-white fw-bold py-3">Contact for Invoice</h6>
      <div class="card-body">
        <div class="mb-3">
          <label class="form-label text-muted mb-0">Will the applying party be the lead contact person for payment?</label><br>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="payment_lead_same" value="Yes" {{ old('payment_lead_same', 'Yes') === 'Yes' ? 'checked' : '' }}>
            <label class="form-check-label">Yes</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="payment_lead_same" value="No" {{ old('payment_lead_same', 'Yes') === 'No' ? 'checked' : '' }}>
            <label class="form-check-label">No</label>
          </div>
        </div>

        <div id="paymentLeadFields" style="display: none;">
          <div class="mb-3">
            <label class="form-label text-muted mb-0">Payment Lead Name</label>
            <input type="text" class="form-control" name="payment_name" value="{{ old('payment_name') }}" maxlength="255">
          </div>
          <div class="mb-3">
            <label class="form-label text-muted mb-0">Institution</label>
            <input type="text" class="form-control" name="payment_institution" value="{{ old('payment_institution') }}" maxlength="255">
          </div>
          <div class="mb-3">
            <label class="form-label text-muted mb-0">Professional Title</label>
            <input type="text" class="form-control" name="payment_title" value="{{ old('payment_title') }}" maxlength="255">
          </div>
          <div class="mb-3">
            <label class="form-label text-muted mb-0">E-mail</label>
            <input type="email" class="form-control" name="payment_email" value="{{ old('payment_email') }}" maxlength="255">
          </div>
          <div class="mb-3">
            <label class="form-label text-muted mb-0">Phone</label>
            <input type="tel" class="form-control" name="payment_phone" value="{{ old('payment_phone') }}" maxlength="255">
          </div>
          <div class="mb-3">
            <label class="form-label text-muted mb-0">Cell Phone</label>
            <input type="tel" class="form-control" name="payment_cell" value="{{ old('payment_cell') }}" maxlength="255">
          </div>
        </div>
      </div>
    </div>

    <!-- 5. Terms & Conditions -->
    <div class="card section-card">
      <h6 class="card-header bg-primary text-white fw-bold py-3">Terms & Conditions</h6>
      <div class="card-body">

        <div style="max-height: 250px; overflow-y: auto; border: 1px solid #dee2e6; padding: 15px; border-radius: 8px; background: #f8f9fa;">
            <h6 class="fw-bold">Terms & Conditions</h6>
            <span class="mb-3">
                - Pre-Conference Workshop applications must be submitted in English.</br>
                - CUGH2027 reserves the right to approve or reject any/all Pre-Conference Workshop applications.</br>
                - Invoices for the Pre-Conference Workshop will be sent to the indicated payment lead within 14 business days of approval.</br>
                - CUGH2027 does not provide Continuing Education (CE) Credits. Organizers may obtain accreditation independently.</br>
                - Workshop organizers are responsible for all promotion and must share their promotional plan in advance.</br>
                - CUGH2027 does not guarantee attendance and will not refund fees based on attendance.</br>
                - Any changes must be pre-approved by the CUGH 2027 Conference Implementation Team.</br>
            </span>

            <h6 class="fw-bold mt-2">Cancellation Policy</h6>
            <span>
                Notice of cancellation must be received in writing. Workshop organizers will be held responsible for 50% of the Workshop Room Rental fee upon cancellation of a confirmed Workshop after December 31ˢᵗ, 2026.
            </span>

            <h6 class="fw-bold mt-2">Important Dates</h6>
            - <span><b class="text-danger">October 15ᵗʰ, 2026:</b> Application deadline.<br>
            - <b class="text-danger">October 31ˢᵗ, 2026:</b> Acceptance notification.<br>
            - <b class="text-danger">December 15ᵗʰ, 2026:</b> Last day to cancel for full refund.<br>
            </span>

            <span class="mt-2 d-block mb-0">
                If you have read the above, please check the box below in lieu of your signature.
            </span>

            </div>

        <div class="mb-3 mt-3 form-check">
          <input type="checkbox" class="form-check-input" name="terms" id="terms" required {{ old('terms') ? 'checked' : '' }}>
          <label class="form-check-label" for="terms">I have read, understood, and agree to the above terms and conditions</label>
        </div>
      </div>
    </div>

    <!-- 6. Signature -->
    <div class="card section-card">
      <h6 class="card-header bg-primary text-white fw-bold py-3">Signature</h6>
      <div class="card-body">

        <div class="row">
            <div class="col-md-4"></div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label text-muted mb-0 d-block text-center">Signature<small class="text-danger">⁽*⁾</small></label>
                    <canvas id="signatureCanvas" class="signature-box"></canvas>
                    <input type="hidden" name="signature" id="signatureInput">
                    <div class="mt-0 text-end">
                        <button type="button" class="btn btn-secondary btn-sm" id="clearSig">
                            Clear
                        </button>
                    </div>
                </div>
                <div class="mb-3">
                    <input type="text" class="form-control text-center" name="place_date" value="{{ date('Y-m-d') }}" readonly>
                </div>
            </div>
            <div class="col-md-4"></div>
        </div>
      </div>
    </div>

    <div class="text-center">
      <button type="submit" class="btn btn-primary btn-lg">Submit Application</button>
    </div>
  </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.1/dist/signature_pad.umd.min.js"></script>

<script src="{{ asset('assets/js/apps/workshops/form-online.js') }}?v={{ filemtime(public_path('assets/js/apps/workshops/form-online.js')) }}"></script>

</body>
</html>
