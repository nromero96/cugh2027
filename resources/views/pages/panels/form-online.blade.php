<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panel Submission</title>
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
            <a class="btn btn-primary" href="https://cughlima2027.org/panel-submission/">
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

  <h2 class="mb-0 text-center fw-bold">Panel Submission</h2>
  <p class="text-center text-muted mb-4">Panel submissions must be made online. Panel proposals submitted via e-mail, post, or other methods will not be accepted.</p>


  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Success!</strong> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>{{__("Error")}}!</strong> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

    <div class="card section-card">
      <h6 class="card-header bg-primary text-white fw-bold py-3">Panel Details</h6>
      <div class="card-body">
        <form id="panelForm" action="{{ route('panels.storeonline') }}" method="POST" enctype="multipart/form-data">
            @csrf
                <!-- LANGUAGES -->
                <div class="mb-2">
                    <label class="form-label text-muted mb-2">Language <small>(Please indicate the language in which you will present the panel.)</small></label><br>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="language" id="languageRadio1" value="English">
                        <label class="form-check-label" for="languageRadio1">English</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="language" id="languageRadio2" value="Spanish">
                        <label class="form-check-label" for="languageRadio2">Spanish</label>
                    </div>                            
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="language" id="languageRadio3" value="PPT Slides in English and Oral Presentation in Spanish">
                        <label class="form-check-label" for="languageRadio3">PPT Slides in English and Oral Presentation in Spanish</label>
                    </div>
                </div>

                            <!-- SUB THEMES -->
                            <div class="mb-3">
                                <label class="form-label text-muted mb-2">
                                    Sub-Themes <small>(Max 3 options)</small>
                                </label><br>

                                <div class="form-check form-check-block">
                                    <input class="form-check-input subtheme-checkbox" type="checkbox" name="subthemes[]" id="subthemes1" value="Non-Communicable Diseases, Health Systems, Public Health, Primary and Surgical Care">
                                    <label class="form-check-label" for="subthemes1">
                                        Non-Communicable Diseases, Health Systems, Public Health, Primary and Surgical Care
                                    </label>
                                </div>

                                <div class="form-check form-check-block">
                                    <input class="form-check-input subtheme-checkbox" type="checkbox" name="subthemes[]" id="subthemes2" value="Social Determinants of Health">
                                    <label class="form-check-label" for="subthemes2">
                                        Social Determinants of Health
                                    </label>
                                </div>

                                <div class="form-check form-check-block">
                                    <input class="form-check-input subtheme-checkbox" type="checkbox" name="subthemes[]" id="subthemes3" value="Environmental Determinants of Health, Planetary Health, One Health, Environmental Health, Climate Change, Biodiversity Crisis, Pollution">
                                    <label class="form-check-label" for="subthemes3">
                                        Environmental Determinants of Health, Planetary Health, One Health, Environmental Health, Climate Change, Biodiversity Crisis, Pollution
                                    </label>
                                </div>

                                <div class="form-check form-check-block">
                                    <input class="form-check-input subtheme-checkbox" type="checkbox" name="subthemes[]" id="subthemes4" value="Communicable Diseases, Pandemic Prevention, Detection and Response, Emerging Infectious Diseases">
                                    <label class="form-check-label" for="subthemes4">
                                        Communicable Diseases, Pandemic Prevention, Detection and Response, Emerging Infectious Diseases
                                    </label>
                                </div>

                                <div class="form-check form-check-block">
                                    <input class="form-check-input subtheme-checkbox" type="checkbox" name="subthemes[]" id="subthemes5" value="Research, Education, Translation and Implementation Science, Bridging Research to Policy, Innovation and Research">
                                    <label class="form-check-label" for="subthemes5">
                                        Research, Education, Translation and Implementation Science, Bridging Research to Policy, Innovation and Research
                                    </label>
                                </div>

                                <div class="form-check form-check-block">
                                    <input class="form-check-input subtheme-checkbox" type="checkbox" name="subthemes[]" id="subthemes6" value="Governance, Political Determinants of Health, Diplomacy, Law, Anti-Corruption, Human Rights, Strengthening Public Institutions">
                                    <label class="form-check-label" for="subthemes6">
                                        Governance, Political Determinants of Health, Diplomacy, Law, Anti-Corruption, Human Rights, Strengthening Public Institutions
                                    </label>
                                </div>

                                <div class="form-check form-check-block">
                                    <input class="form-check-input subtheme-checkbox" type="checkbox" name="subthemes[]" id="subthemes7" value="Other">
                                    <label class="form-check-label" for="subthemes7">
                                        Other
                                    </label>
                                </div>

                                <div class="mt-0 d-none" id="subthemesother">
                                    <input type="text" class="form-control" name="subthemes_other" placeholder="Please specify">
                                </div>
                            </div>

                            <!-- TITLE -->
                            <div class="mb-3">
                                <label class="form-label mb-0"><b>TITLE</b> (max 15 words)</label>
                                <input type="text" class="form-control" name="title" maxlength="150">
                            </div>

                            <!-- POINT OF CONTACT -->
                            <h6>Contact person</h6>
                            <div class="row">
                                <div class="col-md-2 mb-3">
                                    <label class="form-label text-muted mb-0">Salutation</label>
                                    <select class="form-select" name="contact_salutation">
                                        <option>Mr.</option>
                                        <option>Mrs.</option>
                                        <option>Ms.</option>
                                        <option>Dr.</option>
                                        <option>Prof.</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label text-muted mb-0" >Full Name</label>
                                    <input type="text" class="form-control" name="contact_name">
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted mb-0">Institution</label>
                                    <input type="text" class="form-control" name="contact_institution">
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="form-label text-muted mb-0">Country</label>
                                    <select class="form-select" name="contact_country" id="country">
                                        <option value="">Select...</option>
                                        @foreach($countries as $country)
                                            <option value="{{ $country->name }}">{{ $country->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label text-muted mb-0">Cell Phone <small>(Country code & number)</small></label>
                                    <input type="text" class="form-control" name="contact_phone">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted mb-0">E-mail</label>
                                    <input type="email" class="form-control" name="contact_email">
                                </div>
                            </div>

                            <!-- MODERATOR -->
                            <h6>Moderator</h6>
                            <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label text-muted mb-0">Full Name</label>
                                <input type="text" class="form-control" name="moderator_name">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label text-muted mb-0">Position</label>
                                <input type="text" class="form-control" name="moderator_position">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label text-muted mb-0">Institution</label>
                                <input type="text" class="form-control" name="moderator_institution">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label text-muted mb-0">Country</label>
                                <select class="form-select" name="moderator_country" id="country">
                                    <option value="">Select...</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country->name }}">{{ $country->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            </div>

                            <!-- SPEAKERS -->
                            <h6>Speakers</h6>
                            <div id="speakers"></div>

                            <button type="button" class="btn btn-secondary mb-3" onclick="addSpeaker()">Add Speaker</button>

                            <!-- DESCRIPTION -->
                            <div class="mb-3">
                            <label class="form-label text-muted mb-0">Panel Description (max 2000 chars)</label>
                            <textarea class="form-control" maxlength="2000" rows="5" name="description"></textarea>
                            </div>

                            <div class="mb-3">
                            <label class="form-label text-muted mb-0">Learning Objectives (max 2000 chars)</label>
                            <textarea class="form-control" maxlength="2000" rows="5" name="learning_objectives"></textarea>
                            </div>

                            <div class="col-12 text-end">
                                <button type="submit" name="action" class="btn btn-primary" id="submitBtn">Send for Review</button>
                            </div>
                        </form>
      </div>
    </div>
</div>


<script>
let speakerCount = 0;

// 👇 Convert Laravel countries to JS
const countries = @json($countries);

function renderCountryOptions() {
  let options = '<option value="">Select...</option>';
  countries.forEach(c => {
    options += `<option value="${c.name}">${c.name}</option>`;
  });
  return options;
}

function removeSpeaker(button) {
  const speakerDiv = button.closest('.border');
  speakerDiv.remove();

  // Reordenar speakers para evitar huecos en índices
  const allSpeakers = document.querySelectorAll('#speakers > div');
  speakerCount = 0;

  allSpeakers.forEach((div, index) => {
    speakerCount = index + 1;

    // actualizar título
    div.querySelector('h6').textContent = `Speaker ${speakerCount}`;

    // actualizar names
    const inputs = div.querySelectorAll('input, select');
    inputs.forEach(input => {
      if (input.name.includes('[name]')) input.name = `speakers[${speakerCount}][name]`;
      if (input.name.includes('[position]')) input.name = `speakers[${speakerCount}][position]`;
      if (input.name.includes('[institution]')) input.name = `speakers[${speakerCount}][institution]`;
      if (input.name.includes('[country]')) input.name = `speakers[${speakerCount}][country]`;
    });
  });
}

function addSpeaker() {
  if (speakerCount >= 4) return alert("Max 4 speakers");

  speakerCount++;

  const div = document.createElement('div');
  div.classList.add('border','p-3','mb-3');

  div.innerHTML = `
    <div class="d-flex justify-content-between align-items-center mb-2">
      <h6 class="mb-0">Speaker ${speakerCount}</h6>
      <button type="button" class="btn btn-sm btn-danger" onclick="removeSpeaker(this)">Delete</button>
    </div>
    <div class="row">
      <div class="col-md-3 mb-2">
        <input type="text" class="form-control" name="speakers[${speakerCount}][name]" placeholder="Full Name">
      </div>
      <div class="col-md-3 mb-2">
        <input type="text" class="form-control" name="speakers[${speakerCount}][position]" placeholder="Position">
      </div>
      <div class="col-md-3 mb-2">
        <input type="text" class="form-control" name="speakers[${speakerCount}][institution]" placeholder="Institution">
      </div>
      <div class="col-md-3 mb-2">
        <select class="form-select" name="speakers[${speakerCount}][country]">
          ${renderCountryOptions()}
        </select>
      </div>
    </div>
  `;

  document.getElementById('speakers').appendChild(div);
}
</script>

<script>
    const checkboxes = document.querySelectorAll('.subtheme-checkbox');
    const otherCheckbox = document.getElementById('subthemes7');
    const otherInput = document.getElementById('subthemesother');
    const otherInputValue = otherInput.querySelector('input');


    checkboxes.forEach((checkbox) => {
        checkbox.addEventListener('change', function () {

            // Limitar máximo 3 opciones
            const checked = document.querySelectorAll('.subtheme-checkbox:checked');

            if (checked.length > 3) {
                this.checked = false;
                alert('You can only select up to 3 options.');
                return;
            }

            // Mostrar/Ocultar input de Other
            if (otherCheckbox.checked) {
                otherInput.classList.remove('d-none');
                //clear inputs
                
            } else {
                otherInput.classList.add('d-none');
                otherInputValue.value = '';
            }
        });
    });
</script>


<script>
let formSubmitting = false;

document.getElementById('panelForm').addEventListener('submit', function(e) {

    if(formSubmitting){
        e.preventDefault();
        return;
    }

    formSubmitting = true;

    const btn = document.getElementById('submitBtn');

    btn.disabled = true;

    btn.innerHTML = `
        <span class="spinner-border spinner-border-sm me-2"></span>
        Sending...
    `;
});
</script>

</body>
</html>