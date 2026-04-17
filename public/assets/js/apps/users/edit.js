document.addEventListener("DOMContentLoaded", function () {

    //is_cugh_member
    document.addEventListener('change', function (e) {
        if (e.target.matches('input[name="is_cugh_member"]')) {
            const selectedValue = e.target.value;
            const otherDiv = document.getElementById('cugh_member_institution_div');
            const selectOther = document.getElementById('cugh_member_institution');
            if (selectedValue === '1') {
                otherDiv.classList.remove('d-none');
            } else {
                otherDiv.classList.add('d-none');
                selectOther.selectedIndex = 0;
            }
        }
    });


    //inputOccupation if select Other
    document.addEventListener('change', function (e) {
        if (e.target.matches('select[name="occupation"]')) {
            const selectedValue = e.target.value;
            const otherDiv = document.getElementById('occupation_other');
            const inputOther = document.getElementById('inputOtherOccupation');

            if (selectedValue === 'Other') {
                otherDiv.classList.remove('d-none');
                //add input required
                inputOther.setAttribute('required', 'required');
            } else {
                otherDiv.classList.add('d-none');
                //remove input required
                inputOther.removeAttribute('required');
                //remove value input
                inputOther.value = '';
            }
        }
    });


    //inputDegrees if select Other
    document.addEventListener('change', function (e) {
        if (e.target.matches('select[name="degrees"]')) {
            const selectedValue = e.target.value;
            const otherDiv = document.getElementById('other_degrees_div');
            const inputOther = document.getElementById('other_degrees');

            if (selectedValue === 'Other') {
                otherDiv.classList.remove('d-none');
                //add input required
                inputOther.setAttribute('required', 'required');
            } else {
                otherDiv.classList.add('d-none');
                //remove input required
                inputOther.removeAttribute('required');
                //remove value input
                inputOther.value = '';
            }
        }
    });















    toggleOtherCheckbox('input[name="sector[]"][value="Other"]', 'other_sector');
    //AREA(S) OF WORK
    toggleOtherCheckbox('input[name="area_of_work[]"][value="Other"]', 'other_area_of_work');
    //HOW DID YOU HEAR ABOUT THE CUGH CONFERENCE
    toggleOtherCheckbox('input[name="how_did_you_hear_about[]"][value="Other"]', 'other_how_did_you_hear_about');
    //WHY ARE YOU ATTENDING THE CONFERENCE?
    toggleOtherCheckbox('input[name="why_attending[]"][value="Other"]', 'other_why_attending');
    //HOW IS YOUR ATTENDANCE FUNDED?
    toggleOtherCheckbox('input[name="how_is_your_attendance_funded[]"][value="Other"]', 'other_how_is_your_attendance_funded');
    //YOUR AREAS OF FOCUS IN GLOBAL HEALTH
    toggleOtherCheckbox('input[name="your_areas_of_focus_in_global_health[]"][value="Other"]', 'other_your_areas_of_focus_in_global_health');
    //OBSTACLES TO ATTENDING CUGH'S CONFERENCES
    toggleOtherCheckbox('input[name="obstacles_to_attending_cughs_conferences[]"][value="Other"]', 'other_obstacles_to_attending_cughs_conferences');



});


function toggleOtherCheckbox(checkboxSelector, inputId) {
        const checkbox = document.querySelector(checkboxSelector);
        const input = document.getElementById(inputId);

        if (!checkbox || !input) return;

        function toggle() {
            if (checkbox.checked) {
                input.classList.remove('d-none');
            } else {
                input.classList.add('d-none');
                input.value = ''; // opcional: limpia el input
            }
        }

        checkbox.addEventListener('change', toggle);
        toggle(); // ejecutar al cargar
    }