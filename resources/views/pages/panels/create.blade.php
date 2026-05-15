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
                                    Panel Submission
                                </h4>
                            </div>
                        </div>
                    </div>
                    <div class="widget-content widget-content-area pt-0">
                        
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

@endsection