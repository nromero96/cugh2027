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
                            <form class="row g-3" action="{{ route('works.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                <div class="col-md-12">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="presentation_type" id="presentation_type1" value="Oral Presentation" checked>
                                        <label class="form-check-label" for="presentation_type1">Oral Presentation</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="presentation_type" id="presentation_type2" value="Poster">
                                        <label class="form-check-label" for="presentation_type2">Poster</label>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <label for="optionsAbstractType" class="form-label text-muted mb-0">Abstract Type</label>
                                    <select name="abstract_type" class="form-select" id="optionsAbstractType">
                                        <option value="">Select...</option>
                                        <option value="Scientific Abstract">Scientific Abstract</option>
                                        <option value="Program & Project Abstract">Program & Project Abstract</option>
                                        <option value="Global Health Education Abstract">Global Health Education Abstract</option>
                                    </select>
                                </div>

                                <div class="col-md-12">
                                    <label for="selectSubtopic" class="form-label text-muted mb-0">Sub theme</label>
                                    <select name="subtopic" class="form-select" id="selectSubtopic">
                                        <option value="">Select...</option>
                                        <option value="Health Policy">Health Policy</option>
                                        <option value="Health Promotion">Health Promotion</option>
                                        <option value="Health Education">Health Education</option>
                                        <option value="Health Care">Health Care</option>
                                        <option value="Health Care Financing">Health Care Financing</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-12">
                                    <label for="inputName" class="form-label text-muted mb-0">Title</label>
                                    <input type="text" name="title" class="form-control" id="inputTitle">
                                    <small id="charCountTitle">0 / 250</small>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label text-muted mb-0">Co-authors</label>

                                    <div id="container_coauthors"></div>

                                    <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="addRowCoAuthors()">
                                        + Add co-author
                                    </button>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label text-muted mb-0">Institution</label>

                                    <div id="container_institution"></div>

                                    <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="addRowInstitution()">
                                        + Add institution
                                    </button>
                                </div>

                                

                                <div class="col-md-12">
                                    <label for="inputDescription" class="form-label text-muted mb-0">
                                        Body text (
                                        <a href="https://pdfobject.com/pdf/sample.pdf" target="_blank">
                                            <small class="text-info text-decoration-underline">Read the guidelines</small>
                                        </a>
                                        )
                                    </label>
                                    <textarea name="body" class="form-control" id="inputDescription" rows="15" placeholder="...." maxlength="3000"></textarea>
                                    <small id="charCount">0 / 3000</small>
                                </div>

                                <div class="col-md-12">
                                    <label for="inputKeywords" class="form-label text-muted mb-2 d-block">Keywords <small class="text-danger">(Please choose between 1 and 3 keyword(s))</small></label>

                                    <div class="multi-select-container" id="multiTopics">
                                        <div class="tags"></div>
                                        <select id="topicsSelect">
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

                                    <input type="hidden" name="topics[]" id="topicsInput">

                                    
                                </div>

                                
                                <div class="col-12 text-end">
                                    <button type="submit" name="action" class="btn btn-outline-secondary" value="borrador" disabled>Save as Draft</button>
                                    <button type="submit" name="action" class="btn btn-primary" value="finalizado" disabled>Send for Review</button>
                                </div>
                            </form>
                        
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

<script>
function addRowInstitution(value = '') {
    const container = document.getElementById('container_institution');

    const html = `
        <div class="row-institution">
            <div class="number-institution"></div>
            <input type="text" name="institutions[]" class="form-control form-control-sm"
                placeholder="e.g. e.g. Universidad Peruana Cayetano Heredia - Lima - Medical Sciences" value="${value}">
            <button type="button" class="btn-remove-institution" onclick="removeRowInstitution(this)">×</button>
        </div>
    `;

    container.insertAdjacentHTML('beforeend', html);
    updateNumbersInstitution();
}

function removeRowInstitution(btn) {
    const container = document.getElementById('container_institution');
    if(container.children.length <= 1) return; // ❌ nunca eliminar la primera fila

    btn.parentElement.remove();
    updateNumbersInstitution();
}

function updateNumbersInstitution() {
    const numbers = document.querySelectorAll('#container_institution .number-institution');
    numbers.forEach((el, i) => el.innerText = (i + 1) + '.');
}

// ⚡ Inicia con 1 fila por defecto (sin botón eliminar)
window.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('container_institution');

    const html = `
        <div class="row-institution">
            <div class="number-institution">1.</div>
            <input type="text" name="institutions[]" class="form-control form-control-sm"
                placeholder="e.g. Universidad Peruana Cayetano Heredia - Lima - Medical Sciences">
            <button type="button" class="btn-remove-institution" onclick="removeRowInstitution(this)" disabled>×</button>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
});
</script>

<script>
function addRowCoAuthors(value = '') {
    const container = document.getElementById('container_coauthors');

    const html = `
        <div class="row-coauthors">
            <div class="number-coauthors"></div>
            <input type="text" name="co_authors[]" class="form-control form-control-sm"
                placeholder="e.g. John Doe" value="${value}">
            <button type="button" class="btn-remove-coauthors" onclick="removeRowCoAuthors(this)">×</button>
        </div>
    `;

    container.insertAdjacentHTML('beforeend', html);
    updateNumbersCoAuthors();
}

function removeRowCoAuthors(btn) {
    const container = document.getElementById('container_coauthors');
    if(container.children.length <= 1) return; // ❌ nunca eliminar la primera fila

    btn.parentElement.remove();
    updateNumbersCoAuthors();
}

function updateNumbersCoAuthors() {
    const numbers = document.querySelectorAll('#container_coauthors .number-coauthors');
    numbers.forEach((el, i) => el.innerText = (i + 1) + '.');
}

// ⚡ Inicia con 1 fila por defecto (primera fila no eliminable)
window.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('container_coauthors');

    const html = `
        <div class="row-coauthors">
            <div class="number-coauthors">1.</div>
            <input type="text" name="co_authors[]" class="form-control form-control-sm" placeholder="e.g. John Doe">
            <button type="button" class="btn-remove-coauthors" disabled>×</button>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
});
</script>


<script>
const select = document.getElementById('topicsSelect');
const tagsContainer = document.querySelector('#multiTopics .tags');
const hiddenInput = document.getElementById('topicsInput');

let selectedTopics = [];

select.addEventListener('change', () => {
    const value = select.value;
    if(!value) return;

    if(selectedTopics.includes(value)) {
        select.value = '';
        return;
    }

    if(selectedTopics.length >= 3) {
        alert("You can select up to 3 topics only.");
        select.value = '';
        return;
    }

    selectedTopics.push(value);
    renderTags();
    select.value = '';
});

function renderTags() {
    tagsContainer.innerHTML = '';
    selectedTopics.forEach((topic, index) => {
        const tag = document.createElement('div');
        tag.className = 'tag';
        tag.innerHTML = `${topic} <span class="remove-tag" onclick="removeTag(${index})">×</span>`;
        tagsContainer.appendChild(tag);
    });

    // actualizar input oculto para enviar al backend
    hiddenInput.value = JSON.stringify(selectedTopics);
}

function removeTag(index) {
    selectedTopics.splice(index, 1);
    renderTags();
}
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


@endsection