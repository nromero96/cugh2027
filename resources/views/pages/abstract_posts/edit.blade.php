@extends('layouts.app')

@section('content')

<div class="layout-px-spacing">

    <div class="middle-content container-xxl p-0">

        <div class="row layout-spacing">
            <div class="col-lg-12 layout-top-spacing mt-4">
                <div class="statbox widget box box-shadow">
                    <div class="widget-header">
                        <div class="row">
                            <div class="col-xl-6 col-md-6 col-sm-6 mb-2 col-6">
                                <h4>
                                    Abstract N°:{{ $abstract_post->id }}
                                </h4>
                            </div>
                            <div class="col-xl-6 col-md-6 col-sm-6 mb-2 col-6 text-end">
                                <span class="badge bg-light-secondary mt-2">Last Update: {{ $abstract_post->updated_at }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="widget-content widget-content-area pt-0">
                            <form class="row g-3" action="{{ route('abstract_posts.update', $abstract_post->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="col-md-12">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="presentation_type" id="presentation_type1" value="Oral Presentation" {{ old('presentation_type', $abstract_post->presentation_type) == 'Oral Presentation' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="presentation_type1">Oral Presentation</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="presentation_type" id="presentation_type2" value="Poster" {{ old('presentation_type', $abstract_post->presentation_type) == 'Poster' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="presentation_type2">Poster</label>
                                    </div>

                                    @error('presentation_type')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-md-12">
                                    <label for="optionsAbstractType" class="form-label text-muted mb-0">Abstract Type</label>
                                    <select name="abstract_type" class="form-select @error('abstract_type') is-invalid @enderror" id="optionsAbstractType">
                                        <option value="" {{ old('abstract_type', $abstract_post->abstract_type) == '' ? 'selected' : '' }}>Select...</option>
                                        <option value="Scientific Abstract" {{ old('abstract_type', $abstract_post->abstract_type) == 'Scientific Abstract' ? 'selected' : '' }}>Scientific Abstract</option>
                                        <option value="Program & Project Abstract" {{ old('abstract_type', $abstract_post->abstract_type) == 'Program & Project Abstract' ? 'selected' : '' }}>Program & Project Abstract</option>
                                        <option value="Global Health Education Abstract" {{ old('abstract_type', $abstract_post->abstract_type) == 'Global Health Education Abstract' ? 'selected' : '' }}>Global Health Education Abstract</option>
                                    </select>
                                    @error('abstract_type')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-md-12">
                                    <label for="selectSubtopic" class="form-label text-muted mb-0">Sub theme</label>
                                    <select name="subtopic" class="form-select" id="selectSubtopic">
                                        <option value="" {{ old('subtopic', $abstract_post->subtopic) == '' ? 'selected' : '' }}>Select...</option>
                                        <option value="Non-Communicable Diseases, Health Systems, Public Health, Primary and Surgical Care" {{ old('subtopic', $abstract_post->subtopic) == '' ? 'selected' : '' }}>Non-Communicable Diseases, Health Systems, Public Health, Primary and Surgical Care</option>
                                        <option value="Social Determinants of Health" {{ old('subtopic', $abstract_post->subtopic) == 'Social Determinants of Health' ? 'selected' : '' }}>Social Determinants of Health</option>
                                        <option value="Environmental Determinants of Health, Planetary Health, One Health, Environmental Health, Climate Change, Biodiversity Crisis, Pollution" {{ old('subtopic', $abstract_post->subtopic) == 'Environmental Determinants of Health, Planetary Health, One Health, Environmental Health, Climate Change, Biodiversity Crisis, Pollution' ? 'selected' : '' }}>Environmental Determinants of Health, Planetary Health, One Health, Environmental Health, Climate Change, Biodiversity Crisis, Pollution</option>
                                        <option value="Communicable Diseases, Pandemic Prevention, Detection and Response, Emerging Infectious Diseases" {{ old('subtopic', $abstract_post->subtopic) == 'Communicable Diseases, Pandemic Prevention, Detection and Response, Emerging Infectious Diseases' ? 'selected' : '' }}>Communicable Diseases, Pandemic Prevention, Detection and Response, Emerging Infectious Diseases</option>
                                        <option value="Research, Education, Translation and Implementation Science, Bridging Research to Policy, Innovation and Research" {{ old('subtopic', $abstract_post->subtopic) == 'Research, Education, Translation and Implementation Science, Bridging Research to Policy, Innovation and Research' ? 'selected' : '' }}>Research, Education, Translation and Implementation Science, Bridging Research to Policy, Innovation and Research</option>
                                        <option value="Governance, Political Determinants of Health, Diplomacy, Law, Anti-Corruption, Human Rights, Strengthening Public Institutions" {{ old('subtopic', $abstract_post->subtopic) == 'Governance, Political Determinants of Health, Diplomacy, Law, Anti-Corruption, Human Rights, Strengthening Public Institutions' ? 'selected' : '' }}>Governance, Political Determinants of Health, Diplomacy, Law, Anti-Corruption, Human Rights, Strengthening Public Institutions</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-12">
                                    <label for="inputName" class="form-label text-muted mb-0 @error('title') is-invalid @enderror">Title</label>
                                    <input type="text" name="title" class="form-control" id="inputTitle" value="{{ old('title', $abstract_post->title) }}">
                                    <small id="charCountTitle">0 / 250</small>
                                    @error('title')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label text-muted mb-0">Co-authors</label>

                                    <div id="container_coauthors" class="@error('co_authors') border border-danger @enderror"></div>
                                    @error('co_authors')
                                        <small class="text-danger">{{ $message }}</small><br>
                                    @enderror
                                    <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="addRowCoAuthors()">
                                        + Add co-author
                                    </button>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label text-muted mb-0">Institution</label>

                                    <div id="container_institution" class="@error('institution') border border-danger @enderror"></div>
                                    @error('institution')
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
                                    <textarea name="body" class="form-control" id="inputDescription" rows="15" placeholder="...." maxlength="3000">{{ old('body', $abstract_post->body) }}</textarea>
                                    <small id="charCount">0 / 3000</small>
                                    @error('body')
                                        <small class="text-danger">{{ $message }}</small>
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

@php
    $coAuthors = collect(json_decode($abstract_post->co_authors, true))->map(function($c){
        return [
            'id' => $c['id'] ?? 'id_'.uniqid(),
            'name' => $c['name'],
            'lastname' => $c['lastname']
        ];
    })->toArray();
    $institutions = json_decode($abstract_post->institutions, true);
    $keywords = json_decode($abstract_post->keywords, true);
@endphp

<script>

    const OLD_COAUTHORS = @json($coAuthors);
    const OLD_INSTITUTIONS = @json($institutions);
    const OLD_KEYWORDS = @json($keywords);

    console.log(OLD_COAUTHORS);
    console.log(OLD_INSTITUTIONS);

</script>

<script>
    // =============================
    // CONTADORES DE CARACTERES
    // =============================
    function bodyTextCounter(textareaId, counterId, maxChars = 3000) {
        const textarea = document.getElementById(textareaId);
        const charCount = document.getElementById(counterId);

        charCount.innerText = `${textarea.value.length} / ${maxChars}`;

        textarea.addEventListener('input', () => {
            if(textarea.value.length > maxChars) {
                textarea.value = textarea.value.substring(0, maxChars);
            }
            charCount.innerText = `${textarea.value.length} / ${maxChars}`;
        });
    }

    function titleTextCounter(inputId, counterId, maxChars = 250) {
        const input = document.getElementById(inputId);
        const charCount = document.getElementById(counterId);

        charCount.innerText = `${input.value.length} / ${maxChars}`;

        input.addEventListener('input', () => {
            if(input.value.length > maxChars) {
                input.value = input.value.substring(0, maxChars);
            }
            charCount.innerText = `${input.value.length} / ${maxChars}`;
        });
    }

    bodyTextCounter('inputDescription', 'charCount', 3000);
    titleTextCounter('inputTitle', 'charCountTitle', 250);
</script>

<script>
    // =============================
    // KEYWORDS (hasta 3)
    // =============================
    const selectKeywords = document.getElementById('keywordsSelect');
    const tagsContainer = document.querySelector('#multiKeywords .tags');
    const hiddenInput = document.getElementById('keywordsInput');
    let selectedKeywords = Array.isArray(OLD_KEYWORDS) ? OLD_KEYWORDS : [];

    function renderTags() {
        tagsContainer.innerHTML = '';
        selectedKeywords.forEach((topic, index) => {
            const tag = document.createElement('div');
            tag.className = 'tag';
            tag.innerHTML = `${topic} <span class="remove-tag" onclick="removeTag(${index})">×</span>`;
            tagsContainer.appendChild(tag);
        });
        hiddenInput.value = JSON.stringify(selectedKeywords);
    }

    function removeTag(index) {
        selectedKeywords.splice(index, 1);
        renderTags();
    }

    selectKeywords.addEventListener('change', () => {
        const value = selectKeywords.value;
        if(!value) return;
        if(selectedKeywords.includes(value)) { selectKeywords.value = ''; return; }
        if(selectedKeywords.length >= 3) { alert("You can select up to 3 keywords only."); selectKeywords.value = ''; return; }
        selectedKeywords.push(value);
        renderTags();
        selectKeywords.value = '';
    });

    renderTags();
</script>

<script>
    // =============================
    // ABSTRACT TYPE → SUBTOPIC
    // =============================
    const optionsAbstractType = document.getElementById('optionsAbstractType');
    const selectSubtopic = document.getElementById('selectSubtopic');

    optionsAbstractType.addEventListener('change', function() {
        const selectedValue = this.value;
        selectSubtopic.disabled = (selectedValue === 'Global Health Education Abstract');
        if(selectSubtopic.disabled) selectSubtopic.value = '';
    });
</script>

<script>
    // =============================
    // GENERAR ID ÚNICO
    // =============================
    function generateId() {
        return 'id_' + Math.random().toString(36).substr(2, 9);
    }
</script>

<script>
    // =============================
    // CO-AUTHORS
    // =============================
    function addRowCoAuthors(name = '', lastname = '', id = null) {
        const container = document.getElementById('container_coauthors');
        id = id || generateId();

        const html = `
            <div class="row-coauthors d-flex align-items-center gap-2 mb-2" data-id="${id}">
                <div class="number-coauthors"></div>
                <input type="text" name="co_authors_name[]" class="form-control form-control-sm" placeholder="Name" value="${name}" oninput="updateCoAuthorsOptions()">
                <input type="text" name="co_authors_lastname[]" class="form-control form-control-sm" placeholder="Last Name" value="${lastname}" oninput="updateCoAuthorsOptions()">

                <!-- hidden input con ID -->
                <input type="hidden" name="co_authors_id[]" value="${id}">
                <button type="button" class="btn-remove-coauthors" onclick="removeRowCoAuthors(this)">×</button>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
        updateNumbersCoAuthors();
    }

    function removeRowCoAuthors(btn) {
        const row = btn.parentElement;
        const idToRemove = row.dataset.id;
        row.remove();
        updateNumbersCoAuthors();

        // eliminar de instituciones
        document.querySelectorAll('.institution-input').forEach(input => {
            let selected = input.value ? JSON.parse(input.value) : [];
            selected = selected.filter(i => i !== idToRemove);
            input.value = JSON.stringify(selected);
        });

        updateCoAuthorsOptions();
    }

    function updateNumbersCoAuthors() {
        document.querySelectorAll('#container_coauthors .number-coauthors').forEach((el, i) => el.innerText = (i + 1) + '.');
    }

    function getCoAuthorsList() {
        return Array.from(document.querySelectorAll('#container_coauthors .row-coauthors')).map(row => {
            const id = row.dataset.id;
            const name = row.querySelector('[name="co_authors_name[]"]').value.trim();
            const lastname = row.querySelector('[name="co_authors_lastname[]"]').value.trim();
            if(!name && !lastname) return null;
            return { id, text: `${name} ${lastname}`.trim() };
        }).filter(Boolean);
    }
</script>

<script>
    // =============================
    // INSTITUTIONS
    // =============================
    function addRowInstitution(name = '', coauthorsIds = []) {
        const container = document.getElementById('container_institution');
        const html = `
            <div class="row-institution mb-3">
                <div class="d-flex gap-2">
                    <div class="number-institution"></div>
                    <input type="text" name="institutions[]" class="form-control form-control-sm mb-2" placeholder="Institution name" value="${name}">
                    <button type="button" class="btn-remove-institution" onclick="removeRowInstitution(this)">×</button>
                </div>
                <div>
                    <div class="multi-select-container">
                        <div class="tags institution-tags"></div>
                        <select class="coauthor-select form-control form-control-sm">
                            <option value="">Select co-authors...</option>
                        </select>
                    </div>
                    <input type="hidden" name="institution_coauthors[]" class="institution-input" value='${JSON.stringify(coauthorsIds)}'>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
        updateNumbersInstitution();

        const currentRow = container.lastElementChild;
        renderInstitutionTags(currentRow, coauthorsIds);
        updateCoAuthorsOptions();
    }

    function removeRowInstitution(btn) {
        btn.closest('.row-institution').remove();
        updateNumbersInstitution();
        updateCoAuthorsOptions();
    }

    function updateNumbersInstitution() {
        document.querySelectorAll('#container_institution .number-institution').forEach((el, i) => el.innerText = (i + 1) + '.');
    }

    function getUsedCoAuthors() {
        return Array.from(document.querySelectorAll('.institution-input')).flatMap(input => JSON.parse(input.value || '[]'));
    }

    function updateCoAuthorsOptions() {
        const selects = document.querySelectorAll('.coauthor-select');
        const coauthors = getCoAuthorsList();
        const used = getUsedCoAuthors();

        selects.forEach(select => {
            const container = select.closest('.row-institution');
            const inputHidden = container.querySelector('.institution-input');
            const selectedValues = JSON.parse(inputHidden.value || '[]');

            select.innerHTML = `<option value="">Select co-authors...</option>`;

            coauthors.forEach(ca => {
                const option = document.createElement('option');
                option.value = ca.id;
                option.textContent = ca.text;
                select.appendChild(option);
            });

            renderInstitutionTags(container, selectedValues);
        });
    }

    document.addEventListener('change', function(e){
        if(!e.target.classList.contains('coauthor-select')) return;

        const select = e.target;
        const value = select.value;
        if(!value) return;

        const container = select.closest('.row-institution');
        const inputHidden = container.querySelector('.institution-input');
        let selected = JSON.parse(inputHidden.value || '[]');

        if(!selected.includes(value)) selected.push(value);
        inputHidden.value = JSON.stringify(selected);
        renderInstitutionTags(container, selected);
        updateCoAuthorsOptions();
        select.value = '';
    });

    function renderInstitutionTags(container, selected) {
        const tagsContainer = container.querySelector('.institution-tags');
        const coauthors = getCoAuthorsList();
        tagsContainer.innerHTML = '';

        selected.forEach(id => {
            const ca = coauthors.find(c => c.id === id);
            if(!ca) return;
            const tag = document.createElement('div');
            tag.className = 'tag';
            tag.innerHTML = `${ca.text} <span class="remove-tag" onclick="removeInstitutionTag(this, '${ca.id}')">×</span>`;
            tagsContainer.appendChild(tag);
        });
    }

    function removeInstitutionTag(el, id) {
        const container = el.closest('.row-institution');
        const inputHidden = container.querySelector('.institution-input');
        let selected = JSON.parse(inputHidden.value || '[]');
        selected = selected.filter(i => i !== id);
        inputHidden.value = JSON.stringify(selected);
        renderInstitutionTags(container, selected);
        updateCoAuthorsOptions();
    }

    function loadOldInstitutions() {
        if(!OLD_INSTITUTIONS || !OLD_INSTITUTIONS.length) {
            addRowInstitution();
            return;
        }
        OLD_INSTITUTIONS.forEach(inst => {
            addRowInstitution(inst.name ?? '', Array.isArray(inst.coauthors) ? inst.coauthors : []);
        });
    }

    // =============================
    // INICIALIZACIÓN AL CARGAR
    // =============================
    window.addEventListener('DOMContentLoaded', () => {
        // CO-AUTHORS
        if (OLD_COAUTHORS && OLD_COAUTHORS.length > 0) {
            OLD_COAUTHORS.forEach(ca => addRowCoAuthors(ca.name ?? '', ca.lastname ?? '', ca.id));
        } else {
            addRowCoAuthors();
        }

        // INSTITUTIONS
        loadOldInstitutions();

        // KEYWORDS ya cargados antes
        renderTags();

        // Finalmente actualizar selects de co-authors
        updateCoAuthorsOptions();
    });
</script>


@endsection