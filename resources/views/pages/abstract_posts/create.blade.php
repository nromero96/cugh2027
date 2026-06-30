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
                                    Abstract
                                </h4>
                            </div>
                        </div>
                    </div>
                    <div class="widget-content widget-content-area pt-0">
                            <form class="row g-3" action="{{ route('abstract_posts.store') }}" method="POST">
                                @csrf

                                <div class="col-md-12">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="presentation_type" id="presentation_type1" value="Oral Presentation" {{ old('presentation_type', 'Oral Presentation') == 'Oral Presentation' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="presentation_type1">Oral Presentation</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="presentation_type" id="presentation_type2" value="Poster" {{ old('presentation_type') == 'Poster' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="presentation_type2">Poster</label>
                                    </div>
                                    @error('presentation_type')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-md-12">
                                    <label for="optionsAbstractType" class="form-label text-muted mb-0">Abstract Type</label>
                                    <select name="abstract_type" class="form-select @error('abstract_type') is-invalid @enderror" id="optionsAbstractType">
                                        <option value="">Select...</option>
                                        <option value="Scientific Abstract"
                                            {{ old('abstract_type') == 'Scientific Abstract' ? 'selected' : '' }}>
                                            Scientific Abstract
                                        </option>

                                        <option value="Program & Project Abstract"
                                            {{ old('abstract_type') == 'Program & Project Abstract' ? 'selected' : '' }}>
                                            Program & Project Abstract
                                        </option>

                                        <option value="Global Health Education Abstract"
                                            {{ old('abstract_type') == 'Global Health Education Abstract' ? 'selected' : '' }}>
                                            Global Health Education Abstract
                                        </option>
                                    </select>
                                    @error('abstract_type')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-md-12">
                                    <label for="selectSubtopic" class="form-label text-muted mb-0">Sub theme</label>
                                    <select name="subtopic" class="form-select" id="selectSubtopic">
                                        <option value="">Select...</option>

                                        <option value="Non-Communicable Diseases, Health Systems, Public Health, Primary and Surgical Care"
                                            {{ old('subtopic') == 'Non-Communicable Diseases, Health Systems, Public Health, Primary and Surgical Care' ? 'selected' : '' }}>
                                            Non-Communicable Diseases, Health Systems, Public Health, Primary and Surgical Care
                                        </option>

                                        <option value="Social Determinants of Health"
                                            {{ old('subtopic') == 'Social Determinants of Health' ? 'selected' : '' }}>
                                            Social Determinants of Health
                                        </option>

                                        <option value="Environmental Determinants of Health, Planetary Health, One Health, Environmental Health, Climate Change, Biodiversity Crisis, Pollution"
                                            {{ old('subtopic') == 'Environmental Determinants of Health, Planetary Health, One Health, Environmental Health, Climate Change, Biodiversity Crisis, Pollution' ? 'selected' : '' }}>
                                            Environmental Determinants of Health, Planetary Health, One Health, Environmental Health, Climate Change, Biodiversity Crisis, Pollution
                                        </option>

                                        <option value="Communicable Diseases, Pandemic Prevention, Detection and Response, Emerging Infectious Diseases"
                                            {{ old('subtopic') == 'Communicable Diseases, Pandemic Prevention, Detection and Response, Emerging Infectious Diseases' ? 'selected' : '' }}>
                                            Communicable Diseases, Pandemic Prevention, Detection and Response, Emerging Infectious Diseases
                                        </option>

                                        <option value="Research, Education, Translation and Implementation Science, Bridging Research to Policy, Innovation and Research"
                                            {{ old('subtopic') == 'Research, Education, Translation and Implementation Science, Bridging Research to Policy, Innovation and Research' ? 'selected' : '' }}>
                                            Research, Education, Translation and Implementation Science, Bridging Research to Policy, Innovation and Research
                                        </option>

                                        <option value="Governance, Political Determinants of Health, Diplomacy, Law, Anti-Corruption, Human Rights, Strengthening Public Institutions"
                                            {{ old('subtopic') == 'Governance, Political Determinants of Health, Diplomacy, Law, Anti-Corruption, Human Rights, Strengthening Public Institutions' ? 'selected' : '' }}>
                                            Governance, Political Determinants of Health, Diplomacy, Law, Anti-Corruption, Human Rights, Strengthening Public Institutions
                                        </option>
                                    </select>

                                </div>
                                
                                <div class="col-md-12">
                                    <label for="inputName" class="form-label text-muted mb-0">Title</label>
                                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" id="inputTitle" value="{{ old('title') }}">
                                    <small id="charCountTitle">0 / 250</small>
                                    @error('title')
                                        <br><small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-md-12">
                                    <label for="inputName" class="form-label text-muted mb-0">Main author/presenter's name <span class="text-muted">(mandatory)</span></label>
                                    <div id="container_author">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label for="inputName" class="form-label text-muted mb-0">First Name <span class="text-danger">*</span></label>
                                                <input type="text" name="name" class="form-control solo-mayusculas @error('first_name') is-invalid @enderror" id="inputName" value="{{ old('name', $user->name) }}" required>
                                                @error('name')
                                                    <br><small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                            <div class="col-md-4">
                                                <label for="inputName" class="form-label text-muted mb-0">Middle Name</label>
                                                <input type="text" name="lastname" class="form-control solo-mayusculas @error('lastname') is-invalid @enderror" id="inputLastName" value="{{ old('lastname', $user->lastname) }}">
                                                @error('last_name')
                                                    <br><small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                            <div class="col-md-4">
                                                <label for="inputName" class="form-label text-muted mb-0">Last Name <span class="text-danger">*</span></label>
                                                <input type="text" name="second_lastname" class="form-control solo-mayusculas @error('second_lastname') is-invalid @enderror" id="inputSecondLastName" value="{{ old('second_lastname', $user->second_lastname) }}" required>
                                                @error('second_lastname')
                                                    <br><small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                        <span class="text-muted d-block mb-0 mt-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle" viewBox="0 0 16 16">
                                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                                            <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0"/>
                                            </svg> 
                                            <small>This information is shared between the Abstract Submission and Registration forms.</small>
                                        </span>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label text-muted mb-0">Co-authors</label>

                                    <div id="container_coauthors" class=" @error('co_authors') border border-danger @enderror"></div>

                                    @error('co_authors')
                                        <small class="text-danger">{{ $message }}</small><br>
                                    @enderror

                                    <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="addRowCoAuthors()">
                                        + Add co-author
                                    </button>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label text-muted mb-0">Institution</label>

                                    <div id="container_institution" class="@error('institutions') border border-danger @enderror"></div>

                                    @error('institutions')
                                        <small class="text-danger">{{ $message }}</small><br>
                                    @enderror

                                    <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="addRowInstitution()">
                                        + Add institution
                                    </button>
                                </div>

                                

                                <div class="col-md-12">
                                    <label for="inputDescription" class="form-label text-muted mb-0">
                                        Body text 
                                        (<a href="https://cughlima2027.org/wp-content/uploads/2026/04/Abstract-structure-and-text-by-type.pdf" target="_blank"><small class="text-info text-decoration-underline">Read the guidelines</small></a>)

                                        (<a href="https://cughlima2027.org/wp-content/uploads/files/example-abstract.jpeg" target="_blank"><small class="text-info text-decoration-underline">Example</small></a>)

                                    </label>
                                    <textarea name="body" class="form-control @error('body') is-invalid @enderror" id="inputDescription" rows="15" placeholder="...." maxlength="3000">{{ old('body') }}</textarea>
                                    <small id="charCount">0 / 3000</small>
                                    @error('body')
                                        <br><small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-md-12">
                                    <label for="inputKeywords" class="form-label text-muted mb-2 d-block">Keywords <small class="text-danger">(Please choose between 1 and 3 keyword(s))</small></label>

                                    <div class="multi-select-container @error('keywords') border border-danger @enderror" id="multiKeywords">
                                        <div class="tags"></div>
                                        <select id="keywordsSelect">
                                            <option value="" disabled selected>Select a keyword...</option>
                                            <option value="AI">Artificial Intelligence (AI)</option>
                                            <option value="Architecture">Architecture</option>
                                            <option value="Administration/Global Health Operations">Administration/Global Health Operations</option>
                                            <option value="Agriculture">Agriculture</option>
                                            <option value="Anthropology/Sociology">Anthropology/Sociology</option>
                                            <option value="Biodiversity">Biodiversity</option>
                                            <option value="Capacity Building">Capacity Building</option>
                                            <option value="Cancer">Cancer</option>
                                            <option value="Climate Change">Climate Change</option>
                                            <option value="Communication/Advocacy">Communication/Advocacy</option>
                                            <option value="Complex Disaster/Conflict Management">Complex Disaster/Conflict Management</option>
                                            <option value="Corruption/Anti-Corruption Initiatives">Corruption/Anti-Corruption Initiatives</option>
                                            <option value="Displaced Populations/Refugees">Displaced Populations/Refugees</option>
                                            <option value="Economics">Economics</option>
                                            <option value="Education">Education</option>
                                            <option value="Emergency Medicine">Emergency Medicine</option>
                                            <option value="Environment">Environment</option>
                                            <option value="Engineering">Engineering</option>
                                            <option value="Epidemiology/Informatics">Epidemiology/Informatics</option>
                                            <option value="Ethics">Ethics</option>
                                            <option value="Financing">Financing</option>
                                            <option value="Gender Issues">Gender Issues</option>
                                            <option value="Governance">Governance</option>
                                            <option value="Health Systems">Health Systems</option>
                                            <option value="Human Resources">Human Resources</option>
                                            <option value="Human Rights">Human Rights</option>
                                            <option value="Implementation/Translational Science">Implementation/Translational Science</option>
                                            <option value="Infectious Diseases">Infectious Diseases</option>
                                            <option value="Injury, Violence, Trauma">Injury, Violence, Trauma</option>
                                            <option value="Law, Justice">Law, Justice</option>
                                            <option value="Maternal Health">Maternal Health</option>
                                            <option value="Medicine">Medicine</option>
                                            <option value="Mental Health">Mental Health</option>
                                            <option value="Non communicable diseases">Non communicable diseases</option>
                                            <option value="Non-governmental Organization Conduct/Responsibilities">Non-governmental Organization Conduct/Responsibilities</option>
                                            <option value="NTDs">NTDs</option>
                                            <option value="Nursing">Nursing</option>
                                            <option value="Nutrition">Nutrition</option>
                                            <option value="Occupational Health">Occupational Health</option>
                                            <option value="Oral Health/Dentistry">Oral Health/Dentistry</option>
                                            <option value="Pediatrics">Pediatrics</option>
                                            <option value="Pharmacology">Pharmacology</option>
                                            <option value="Policy Development">Policy Development</option>
                                            <option value="Politics/Political Determinants of Health">Politics/Political Determinants of Health</option>
                                            <option value="Pollution">Pollution</option>
                                            <option value="Public Administration">Public Administration</option>
                                            <option value="Public Health">Public Health</option>
                                            <option value="Regulatory Systems">Regulatory Systems</option>
                                            <option value="Research, Research Translation">Research, Research Translation</option>
                                            <option value="Sexual and Reproductive Health">Sexual and Reproductive Health</option>
                                            <option value="Social Determinants of Health">Social Determinants of Health</option>
                                            <option value="Social Sciences/Anthropology">Social Sciences/Anthropology</option>
                                            <option value="Supply Chain">Supply Chain</option>
                                            <option value="Surgery">Surgery</option>
                                            <option value="Technology and Innovations">Technology and Innovations</option>
                                            <option value="Toxicology">Toxicology</option>
                                            <option value="Urban Planning">Urban Planning</option>
                                            <option value="Veterinary Sciences, Animal Health">Veterinary Sciences, Animal Health</option>
                                            <option value="Water/Sanitation">Water/Sanitation</option>
                                            <option value="Women´s Health/Empowerment/Equity">Women´s Health/Empowerment/Equity</option>
                                            <option value="Workforce">Workforce</option>
                                        </select>
                                    </div>

                                    <input type="hidden" name="keywords" id="keywordsInput">

                                    @error('keywords')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror

                                    
                                </div>

                                
                                <div class="col-12 text-end">
                                    <button type="submit" name="action" class="btn btn-outline-secondary" value="draft">Save as Draft</button>
                                    <button type="submit" name="action" class="btn btn-primary" value="submitted">Send for Review</button>
                                </div>
                            </form>
                        
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>



<script>
const select = document.getElementById('keywordsSelect');
const tagsContainer = document.querySelector('#multiKeywords .tags');
const hiddenInput = document.getElementById('keywordsInput');

let selectedKeywords = [];

select.addEventListener('change', () => {
    const value = select.value;
    if(!value) return;

    if(selectedKeywords.includes(value)) {
        select.value = '';
        return;
    }

    if(selectedKeywords.length >= 3) {
        alert("You can select up to 3 keywords only.");
        select.value = '';
        return;
    }

    selectedKeywords.push(value);
    renderTags();
    select.value = '';
});

function renderTags() {
    tagsContainer.innerHTML = '';
    selectedKeywords.forEach((topic, index) => {
        const tag = document.createElement('div');
        tag.className = 'tag';
        tag.innerHTML = `${topic} <span class="remove-tag" onclick="removeTag(${index})">×</span>`;
        tagsContainer.appendChild(tag);
    });

    // actualizar input oculto para enviar al backend
    hiddenInput.value = JSON.stringify(selectedKeywords);
}

function removeTag(index) {
    selectedKeywords.splice(index, 1);
    renderTags();
}


const optionsAbstractType = document.getElementById('optionsAbstractType');
const selectSubtopic = document.getElementById('selectSubtopic');

optionsAbstractType.addEventListener('change', function() {
    const selectedValue = this.value;

    if (selectedValue === 'Global Health Education Abstract') {
        selectSubtopic.value = '';
        selectSubtopic.disabled = true;
    } else {
        selectSubtopic.disabled = false;
    }
});

</script>

<script>
function bodyTextCounter(textareaId, counterId, maxChars = 3000) {
    const textarea = document.getElementById(textareaId);
    const charCount = document.getElementById(counterId);

    textarea.addEventListener('input', () => {
        // cortar si supera maxChars
        if(textarea.value.length > maxChars) {
            textarea.value = textarea.value.substring(0, maxChars);
        }

        // actualizar contador
        charCount.innerText = `${textarea.value.length} / ${maxChars}`;
    });
}

// ⚡ Inicializar el contador
bodyTextCounter('inputDescription', 'charCount', 3000);


function titleTextCounter(inputId, counterId, maxChars = 250) {
    const input = document.getElementById(inputId);
    const charCount = document.getElementById(counterId);

    input.addEventListener('input', () => {
        // cortar si supera maxChars
        if(input.value.length > maxChars) {
            input.value = input.value.substring(0, maxChars);
        }

        // actualizar contador
        charCount.innerText = `${input.value.length} / ${maxChars}`;
    });
}

// ⚡ Inicializar contador para Title
titleTextCounter('inputTitle', 'charCountTitle', 250);

</script>


<script>
// =============================
// GENERADOR DE IDs ÚNICOS
// =============================
function generateId() {
    return 'id_' + Math.random().toString(36).substr(2, 9);
}

// =============================
// CO-AUTHORS
// =============================
function addRowCoAuthors(name = '', lastname = '') {
    const container = document.getElementById('container_coauthors');
    const id = generateId();

    const html = `
        <div class="row-coauthors d-flex align-items-center gap-2 mb-2" data-id="${id}">
            <div class="number-coauthors"></div>

            <input type="text" name="co_authors_name[]" oninput="updateCoAuthorsOptions()" class="form-control form-control-sm" placeholder="Name" value="${name}">
            <input type="text" name="co_authors_lastname[]" oninput="updateCoAuthorsOptions()" class="form-control form-control-sm" placeholder="Last Name" value="${lastname}">

            <!-- hidden input con ID -->
            <input type="hidden" name="co_authors_id[]" value="${id}">

            <button type="button" class="btn-remove-coauthors" onclick="removeRowCoAuthors(this)">×</button>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
    updateNumbersCoAuthors();
    updateCoAuthorsOptions();
}

function removeRowCoAuthors(btn) {
    const container = document.getElementById('container_coauthors');
    if (container.children.length <= 1) return;

    btn.parentElement.remove();
    updateNumbersCoAuthors();
    updateCoAuthorsOptions();
}

function updateNumbersCoAuthors() {
    const numbers = document.querySelectorAll('#container_coauthors .number-coauthors');
    numbers.forEach((el, i) => el.innerText = (i + 1) + '.');
}

// =============================
// GET CO-AUTHORS (SIN VACÍOS)
// =============================
function getCoAuthorsList() {
    const rows = document.querySelectorAll('#container_coauthors .row-coauthors');
    let list = [];

    rows.forEach(row => {
        const id = row.dataset.id;
        const name = row.querySelector('[name="co_authors_name[]"]').value.trim();
        const lastname = row.querySelector('[name="co_authors_lastname[]"]').value.trim();

        if (!name && !lastname) return;

        list.push({
            id: id,
            text: `${name || ''} ${lastname || ''}`.trim()
        });
    });

    return list;
}

// =============================
// GET USED CO-AUTHORS
// =============================
function getUsedCoAuthors() {
    const inputs = document.querySelectorAll('.institution-input');
    let used = [];

    inputs.forEach(input => {
        let values = input.value ? JSON.parse(input.value) : [];
        used = used.concat(values);
    });

    return used;
}

// =============================
// INSTITUTIONS
// =============================
function addRowInstitution(value = '') {
    const container = document.getElementById('container_institution');

    const html = `
        <div class="row-institution mb-3">
            <div class="d-flex gap-2">
                <div class="number-institution"></div>

                <input type="text" name="institutions[]" 
                    class="form-control form-control-sm mb-2"
                    placeholder="Institution name" value="${value}">

                <button type="button" class="btn-remove-institution" onclick="removeRowInstitution(this)">×</button>
            </div>
            <div>
                <div class="multi-select-container">
                    <div class="tags institution-tags"></div>

                    <select class="coauthor-select form-control form-control-sm">
                        <option value="">Please select co-authors that work at the same institution...</option>
                    </select>
                </div>
                <input type="hidden" name="institution_coauthors[]" class="institution-input">
            </div>
        </div>
    `;

    container.insertAdjacentHTML('beforeend', html);
    updateNumbersInstitution();
    updateCoAuthorsOptions();
}

function removeRowInstitution(btn) {
    const container = document.getElementById('container_institution');
    if (container.children.length <= 1) return;

    btn.closest('.row-institution').remove();
    updateNumbersInstitution();
    updateCoAuthorsOptions();
}

function updateNumbersInstitution() {
    const numbers = document.querySelectorAll('#container_institution .number-institution');
    numbers.forEach((el, i) => el.innerText = (i + 1) + '.');
}

// =============================
// UPDATE SELECTS
// =============================
function updateCoAuthorsOptions() {
    const selects = document.querySelectorAll('.coauthor-select');
    const coauthors = getCoAuthorsList();
    const used = getUsedCoAuthors();

    selects.forEach(select => {
        const container = select.closest('.row-institution');
        const input = container.querySelector('.institution-input');
        const selectedValues = input.value ? JSON.parse(input.value) : [];

        select.innerHTML = `<option value="">Please select co-authors that work at the same institution...</option>`;

        coauthors.forEach(ca => {
            const option = document.createElement('option');
            option.value = ca.id;
            option.textContent = ca.text;
            select.appendChild(option);
        });
    });

    refreshAllInstitutionTags(); // 🔥 FIX TIEMPO REAL
}

// =============================
// SELECT → TAG
// =============================
document.addEventListener('change', function(e) {
    if (!e.target.classList.contains('coauthor-select')) return;

    const select = e.target;
    const value = select.value;
    if (!value) return;

    const container = select.closest('.row-institution');
    const inputHidden = container.querySelector('.institution-input');

    let selected = inputHidden.value ? JSON.parse(inputHidden.value) : [];

    if (selected.includes(value)) {
        select.value = '';
        return;
    }

    selected.push(value);
    inputHidden.value = JSON.stringify(selected);

    renderInstitutionTags(container, selected);
    updateCoAuthorsOptions();

    select.value = '';
});

// =============================
// RENDER TAGS
// =============================
function renderInstitutionTags(container, selected) {
    const tagsContainer = container.querySelector('.institution-tags');
    const coauthors = getCoAuthorsList();

    tagsContainer.innerHTML = '';

    selected.forEach((id, index) => {
        const ca = coauthors.find(c => c.id == id);
        if (!ca) return;

        const tag = document.createElement('div');
        tag.className = 'tag';
        tag.innerHTML = `
            ${ca.text} 
            <span class="remove-tag" onclick="removeInstitutionTag(this, ${index})">×</span>
        `;

        tagsContainer.appendChild(tag);
    });
}

// =============================
// REMOVE TAG
// =============================
function removeInstitutionTag(el, index) {
    const container = el.closest('.row-institution');
    const inputHidden = container.querySelector('.institution-input');

    let selected = JSON.parse(inputHidden.value || '[]');
    selected.splice(index, 1);

    inputHidden.value = JSON.stringify(selected);

    renderInstitutionTags(container, selected);
    updateCoAuthorsOptions();
}

// =============================
// REFRESH TAGS (FIX TIEMPO REAL)
// =============================
function refreshAllInstitutionTags() {
    const rows = document.querySelectorAll('.row-institution');

    rows.forEach(row => {
        const input = row.querySelector('.institution-input');
        let selected = input.value ? JSON.parse(input.value) : [];

        renderInstitutionTags(row, selected);
    });
}

// =============================
// INIT
// =============================
window.addEventListener('DOMContentLoaded', () => {

    // -----------------------------
    // CO-AUTHOR inicial
    // -----------------------------
    const id = generateId();
    const c1 = `
        <div class="row-coauthors d-flex align-items-center gap-2 mb-2" data-id="${id}">
            <div class="number-coauthors">1.</div>

            <input type="text" name="co_authors_name[]" 
                oninput="updateCoAuthorsOptions()"
                class="form-control form-control-sm" oninput="updateCoAuthorsOptions()" placeholder="Name">

            <input type="text" name="co_authors_lastname[]" 
                oninput="updateCoAuthorsOptions()"
                class="form-control form-control-sm" oninput="updateCoAuthorsOptions()" placeholder="Last Name">

            <!-- 🔥 Hidden input con ID -->
            <input type="hidden" name="co_authors_id[]" value="${id}">

            <button type="button" class="btn-remove-coauthors" disabled>×</button>
        </div>
    `;
    document.getElementById('container_coauthors').insertAdjacentHTML('beforeend', c1);

    // -----------------------------
    // INSTITUTION inicial
    // -----------------------------
    const i1 = `
        <div class="row-institution mb-3">
            <div class="d-flex gap-2">
                <div class="number-institution">1.</div>

                <input type="text" name="institutions[]" 
                    class="form-control form-control-sm mb-2"
                    placeholder="Institution name">

                <button type="button" class="btn-remove-institution" disabled>×</button>
            </div>
            <div>
                <div class="multi-select-container">
                    <div class="tags institution-tags"></div>

                    <select class="coauthor-select form-control form-control-sm">
                        <option value="">Please select co-authors that work at the same institution...</option>
                    </select>
                </div>

                <!-- hidden input para guardar IDs -->
                <input type="hidden" name="institution_coauthors[]" class="institution-input">
            </div>
        </div>
    `;
    document.getElementById('container_institution').insertAdjacentHTML('beforeend', i1);

    // Actualizar selects de co-authors para instituciones
    updateCoAuthorsOptions();
    
});



</script>

<script>
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('solo-mayusculas')) {
            e.target.value = e.target.value
                .toUpperCase() // Convertir a mayúsculas
                .replace(/[^A-ZÁÉÍÓÚÑ\s]/g, ''); // Permitir solo letras y espacios
        }
    });
</script>


@endsection