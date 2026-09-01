@extends('layouts.app')

@section('content')
@php
    $languages = [
        'English',
        'Spanish',
        'PPT Slides in English and Oral Presentation in Spanish',
    ];
    $subthemeOptions = [
        'Non-Communicable Diseases, Health Systems, Public Health, Primary and Surgical Care',
        'Social Determinants of Health',
        'Environmental Determinants of Health, Planetary Health, One Health, Environmental Health, Climate Change, Biodiversity Crisis, Pollution',
        'Communicable Diseases, Pandemic Prevention, Detection and Response, Emerging Infectious Diseases',
        'Research, Education, Translation and Implementation Science, Bridging Research to Policy, Innovation and Research',
        'Governance, Political Determinants of Health, Diplomacy, Law, Anti-Corruption, Human Rights, Strengthening Public Institutions',
        'Other',
    ];
    $selectedSubthemes = old('subthemes', $panel->subthemes ?? []);
    $selectedSubthemes = is_array($selectedSubthemes) ? $selectedSubthemes : [];
    $selectedSpeakers = old('speakers', $panel->speakers ?? []);
    $selectedSpeakers = is_array($selectedSpeakers) ? $selectedSpeakers : [];
@endphp

<div class="layout-px-spacing">
    <div class="middle-content container-xxl p-0">
        <div class="row layout-spacing">
            <div class="col-lg-12 layout-top-spacing mt-4">
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger" role="alert">
                        <strong>There were validation errors. Please review the form and try again.</strong>
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="statbox widget box box-shadow">
                    <div class="widget-header">
                        <div class="row">
                            <div class="col-8"><h4>Edit Panel #{{ $panel->id }}</h4></div>
                            <div class="col-4 text-end pt-2">
                                <a href="{{ route('panels.show', $panel) }}" class="btn btn-outline-secondary btn-sm">Cancel</a>
                            </div>
                        </div>
                    </div>

                    <div class="widget-content widget-content-area pt-0">
                        <form id="panelEditForm" class="row g-3" action="{{ route('panels.update', $panel) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="col-12">
                                <label class="form-label fw-bold">Language</label>
                                <div>
                                    @foreach($languages as $index => $language)
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="language" id="language{{ $index }}" value="{{ $language }}" {{ old('language', $panel->language) === $language ? 'checked' : '' }} required>
                                            <label class="form-check-label" for="language{{ $index }}">{{ $language }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold">Sub-Themes <small class="text-muted">(Max 3 options)</small></label>
                                @foreach($subthemeOptions as $index => $subtheme)
                                    <div class="form-check">
                                        <input class="form-check-input subtheme-checkbox" type="checkbox" name="subthemes[]" id="subtheme{{ $index }}" value="{{ $subtheme }}" {{ in_array($subtheme, $selectedSubthemes, true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="subtheme{{ $index }}">{{ $subtheme }}</label>
                                    </div>
                                @endforeach
                                <div id="subthemeOtherContainer" class="mt-2 {{ in_array('Other', $selectedSubthemes, true) ? '' : 'd-none' }}">
                                    <input id="subthemeOther" type="text" class="form-control" name="subthemes_other" value="{{ old('subthemes_other', $panel->subthemes_other) }}" placeholder="Please specify">
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold">Title <small class="text-muted">(Max 15 words)</small></label>
                                <input type="text" class="form-control" name="title" maxlength="150" value="{{ old('title', $panel->title) }}" required>
                            </div>

                            <div class="col-12"><hr><h5>Contact Person</h5></div>
                            <div class="col-md-2">
                                <label class="form-label">Salutation</label>
                                <select class="form-select" name="contact_salutation" required>
                                    @foreach(['Mr.', 'Mrs.', 'Ms.', 'Dr.', 'Prof.'] as $salutation)
                                        <option value="{{ $salutation }}" {{ old('contact_salutation', $panel->contact_salutation) === $salutation ? 'selected' : '' }}>{{ $salutation }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Full Name</label>
                                <input type="text" class="form-control" name="contact_name" value="{{ old('contact_name', $panel->contact_name) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Institution</label>
                                <input type="text" class="form-control" name="contact_institution" value="{{ old('contact_institution', $panel->contact_institution) }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Country</label>
                                <select class="form-select" name="contact_country">
                                    <option value="">Select...</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country->name }}" {{ old('contact_country', $panel->contact_country) === $country->name ? 'selected' : '' }}>{{ $country->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Cell Phone</label>
                                <input type="text" class="form-control" name="contact_phone" value="{{ old('contact_phone', $panel->contact_phone) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">E-mail</label>
                                <input type="email" class="form-control" name="contact_email" value="{{ old('contact_email', $panel->contact_email) }}" required>
                            </div>

                            <div class="col-12"><hr><h5>Moderator</h5></div>
                            <div class="col-md-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" class="form-control" name="moderator_name" value="{{ old('moderator_name', $panel->moderator_name) }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Position</label>
                                <input type="text" class="form-control" name="moderator_position" value="{{ old('moderator_position', $panel->moderator_position) }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Institution</label>
                                <input type="text" class="form-control" name="moderator_institution" value="{{ old('moderator_institution', $panel->moderator_institution) }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Country</label>
                                <select class="form-select" name="moderator_country">
                                    <option value="">Select...</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country->name }}" {{ old('moderator_country', $panel->moderator_country) === $country->name ? 'selected' : '' }}>{{ $country->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12"><hr><h5>Speakers</h5></div>
                            <div id="speakers" class="col-12"></div>
                            <div class="col-12">
                                <button type="button" id="addSpeakerButton" class="btn btn-outline-primary btn-sm">Add Speaker</button>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold">Panel Description <small class="text-muted">(Max 2000 characters)</small></label>
                                <textarea class="form-control" maxlength="2000" rows="6" name="description" required>{{ old('description', $panel->description) }}</textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">Learning Objectives <small class="text-muted">(Max 2000 characters)</small></label>
                                <textarea class="form-control" maxlength="2000" rows="6" name="learning_objectives" required>{{ old('learning_objectives', $panel->learning_objectives) }}</textarea>
                            </div>

                            <div class="col-12 text-end">
                                <a href="{{ route('panels.show', $panel) }}" class="btn btn-outline-secondary">Cancel</a>
                                <button type="submit" id="updatePanelButton" class="btn btn-primary">Update Panel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const countries = @json($countries->pluck('name')->values());
const initialSpeakers = @json($selectedSpeakers);
const speakersContainer = document.getElementById('speakers');

function countryOptions(selectedCountry) {
    const fragment = document.createDocumentFragment();
    const emptyOption = document.createElement('option');
    emptyOption.value = '';
    emptyOption.textContent = 'Select...';
    fragment.appendChild(emptyOption);

    countries.forEach(function (country) {
        const option = document.createElement('option');
        option.value = country;
        option.textContent = country;
        option.selected = country === selectedCountry;
        fragment.appendChild(option);
    });

    return fragment;
}

function renumberSpeakers() {
    speakersContainer.querySelectorAll('.speaker-row').forEach(function (row, index) {
        row.querySelector('.speaker-title').textContent = `Speaker ${index + 1}`;
        row.querySelectorAll('[data-speaker-field]').forEach(function (field) {
            field.name = `speakers[${index}][${field.dataset.speakerField}]`;
        });
    });
}

function addSpeaker(speaker = {}) {
    if (speakersContainer.children.length >= 5) {
        alert('You may add up to 5 speakers.');
        return;
    }

    const row = document.createElement('div');
    row.className = 'speaker-row border rounded p-3 mb-3';
    row.innerHTML = `
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="speaker-title mb-0"></h6>
            <button type="button" class="btn btn-sm btn-outline-danger remove-speaker">Delete</button>
        </div>
        <div class="row g-2">
            <div class="col-md-3"><input type="text" class="form-control" data-speaker-field="name" placeholder="Full Name"></div>
            <div class="col-md-3"><input type="text" class="form-control" data-speaker-field="position" placeholder="Position"></div>
            <div class="col-md-3"><input type="text" class="form-control" data-speaker-field="institution" placeholder="Institution"></div>
            <div class="col-md-3"><select class="form-select" data-speaker-field="country"></select></div>
        </div>`;

    row.querySelector('[data-speaker-field="name"]').value = speaker.name || '';
    row.querySelector('[data-speaker-field="position"]').value = speaker.position || '';
    row.querySelector('[data-speaker-field="institution"]').value = speaker.institution || '';
    row.querySelector('[data-speaker-field="country"]').appendChild(countryOptions(speaker.country || ''));
    row.querySelector('.remove-speaker').addEventListener('click', function () {
        row.remove();
        renumberSpeakers();
    });

    speakersContainer.appendChild(row);
    renumberSpeakers();
}

initialSpeakers.slice(0, 5).forEach(addSpeaker);
document.getElementById('addSpeakerButton').addEventListener('click', function () { addSpeaker(); });

const subthemeCheckboxes = document.querySelectorAll('.subtheme-checkbox');
const otherCheckbox = Array.from(subthemeCheckboxes).find(checkbox => checkbox.value === 'Other');
const otherContainer = document.getElementById('subthemeOtherContainer');
const otherInput = document.getElementById('subthemeOther');

function updateOtherSubtheme() {
    otherContainer.classList.toggle('d-none', !otherCheckbox.checked);
    otherInput.required = otherCheckbox.checked;
    if (!otherCheckbox.checked) otherInput.value = '';
}

subthemeCheckboxes.forEach(function (checkbox) {
    checkbox.addEventListener('change', function () {
        const checked = document.querySelectorAll('.subtheme-checkbox:checked');
        if (checked.length > 3) {
            checkbox.checked = false;
            alert('You may select up to 3 sub-themes.');
        }
        updateOtherSubtheme();
    });
});
updateOtherSubtheme();

document.getElementById('panelEditForm').addEventListener('submit', function () {
    const button = document.getElementById('updatePanelButton');
    button.disabled = true;
    button.textContent = 'Updating...';
});
</script>
@endsection
