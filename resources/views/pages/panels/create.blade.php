@extends('layouts.app')

@section('content')

<div class="layout-px-spacing">

    <div class="middle-content container-xxl p-0">

        <div class="row layout-spacing">
            <div class="col-lg-12 layout-top-spacing mt-4">
                <div class="statbox widget box box-shadow">
                    <div class="widget-header">
                        <div class="row">
                            <div class="col-xl-12 col-md-12 col-sm-12 mb-2 col-12">
                                <h4>
                                    Panel Information
                                </h4>
                            </div>
                        </div>
                    </div>
                    <div class="widget-content widget-content-area pt-0">
                        <form>
                            @csrf

                            <!-- LANGUAGES -->
                            <div class="mb-2">
                                <label class="form-label text-muted mb-2">Languages <small>(Please indicate the language in which you will present the panel.)</small></label><br>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio1" value="option1">
                                    <label class="form-check-label" for="inlineRadio1">English</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio2" value="option2">
                                    <label class="form-check-label" for="inlineRadio2">Spanish</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio3" value="option3">
                                    <label class="form-check-label" for="inlineRadio3">PPT Slides in English and Oral Presentation in Spanish</label>
                            </div>

                            <!-- SUB THEMES -->
                            <div class="mb-2">
                                <label class="form-label text-muted mb-2">Sub Themes</label><br>
                                <div class="form-check form-check-block">
                                    <input class="form-check-input" type="Checkbox" name="subthemes" id="subthemes1" value="option1">
                                    <label class="form-check-label" for="subthemes1">Non-Communicable Diseases, Health Systems, Public Health, Primary and Surgical Care</label>
                                </div>
                                <div class="form-check form-check-block">
                                    <input class="form-check-input" type="Checkbox" name="subthemes" id="subthemes2" value="option2">
                                    <label class="form-check-label" for="subthemes2">Social Determinants of Health</label>
                                </div>
                                <div class="form-check form-check-block">
                                    <input class="form-check-input" type="Checkbox" name="subthemes" id="subthemes3" value="option3">
                                    <label class="form-check-label" for="subthemes3">Environmental Determinants of Health, Planetary Health, One Health, Environmental Health, Climate Change, Biodiversity Crisis, Pollution</label>
                                </div>
                                <div class="form-check form-check-block">
                                    <input class="form-check-input" type="Checkbox" name="subthemes" id="subthemes4" value="option4">
                                    <label class="form-check-label" for="subthemes4">Communicable Diseases, Pandemic Prevention, Detection and Response, Emerging Infectious Diseases</label>
                                </div>
                                <div class="form-check form-check-block">
                                    <input class="form-check-input" type="Checkbox" name="subthemes" id="subthemes5" value="option5">
                                    <label class="form-check-label" for="subthemes5">Research, Education, Translation and Implementation Science, Bridging Research to Policy, Innovation and Research</label>
                                </div>
                                <div class="form-check form-check-block">
                                    <input class="form-check-input" type="Checkbox" name="subthemes" id="subthemes6" value="option6">
                                    <label class="form-check-label" for="subthemes6">Governance, Political Determinants of Health, Diplomacy, Law, Anti-Corruption, Human Rights, Strengthening Public Institutions</label>
                                </div>
                                <div class="form-check form-check-block">
                                    <input class="form-check-input" type="Checkbox" name="subthemes" id="subthemes7" value="option7">
                                    <label class="form-check-label" for="subthemes7">Other</label>
                                </div>
                                <div class="form-check form-check-block d-none" id="subthemesother">
                                    <input type="text" class="form-control" placeholder="Please specify">
                                </div>
                            </div>

                            <!-- TITLE -->
                            <div class="mb-3">
                                <label class="form-label text-muted mb-0">Title (max 15 words)</label>
                                <input type="text" class="form-control" maxlength="150">
                            </div>

                            <!-- POINT OF CONTACT -->
                            <h6>Contact person</h6>
                            <div class="row">
                                <div class="col-md-2 mb-3">
                                    <label class="form-label text-muted mb-0">Title</label>
                                    <select class="form-select">
                                    <option>Mr.</option>
                                    <option>Mrs.</option>
                                    <option>Ms.</option>
                                    <option>Dr.</option>
                                    <option>Prof.</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label text-muted mb-0">Name</label>
                                    <input type="text" class="form-control">
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted mb-0">Institution</label>
                                    <input type="text" class="form-control">
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="form-label text-muted mb-0">Country</label>
                                    <select class="form-select" name="country" id="country">
                                        <option value="">Select...</option>
                                        @foreach($countries as $country)
                                            <option value="{{ $country->id }}">{{ $country->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label text-muted mb-0">Phone <small>(Country code & number)</small></label>
                                    <input type="text" class="form-control">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted mb-0">Email</label>
                                    <input type="email" class="form-control">
                                </div>
                            </div>

                            <!-- MODERATOR -->
                            <h6>Moderator</h6>
                            <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label text-muted mb-0">Name</label>
                                <input type="text" class="form-control">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label text-muted mb-0">Position</label>
                                <input type="text" class="form-control">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label text-muted mb-0">Institution</label>
                                <input type="text" class="form-control">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label text-muted mb-0">Country</label>
                                <select class="form-select" name="country" id="country">
                                    <option value="">Select...</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country->id }}">{{ $country->name }}</option>
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
                            <textarea class="form-control" maxlength="2000" rows="5"></textarea>
                            </div>

                            <div class="mb-3">
                            <label class="form-label text-muted mb-0">Learning Objectives (max 2000 chars)</label>
                            <textarea class="form-control" maxlength="2000" rows="5"></textarea>
                            </div>

                            <div class="col-12 text-end">
                                <button type="submit" name="action" class="btn btn-primary" value="submitted" disabled>Send for Review</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
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
    options += `<option value="${c.id}">${c.name}</option>`;
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
      <button type="button" class="btn btn-sm btn-danger" onclick="removeSpeaker(this)">Eliminar</button>
    </div>
    <div class="row">
      <div class="col-md-3 mb-2">
        <input type="text" class="form-control" placeholder="Name">
      </div>
      <div class="col-md-3 mb-2">
        <input type="text" class="form-control" placeholder="Position">
      </div>
      <div class="col-md-3 mb-2">
        <input type="text" class="form-control" placeholder="Institution">
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

@endsection