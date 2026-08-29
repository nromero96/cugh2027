document.addEventListener("DOMContentLoaded", function () {

    const formInscription = document.getElementById("formInscription");
    var btnSaveInscription = document.getElementById("btnSaveInscription");
    var btnSubInscription = document.getElementById("btnSubInscription");

    formInscription.addEventListener("submit", function (event) {
        const action = event.submitter ? event.submitter.value : null; // save or register

        if (!action) {
            event.preventDefault();
            return;
        }

        document.getElementById('formAction').value = action;

        if (action === "register") {
            btnSubInscription.textContent = "Processing...";
            if (!validarCamposInscription()) {
                event.preventDefault();
                btnSubInscription.textContent = "Register Now";
            } else {
                btnSubInscription.disabled = true;
                btnSaveInscription.disabled = true;
            }
        }

        if (action === "save") {
            btnSaveInscription.textContent = "Saving draft...";
            if (!validarCamposInscriptionDraft()) {
                event.preventDefault();
                btnSaveInscription.textContent = "Save Draft";
            } else {
                btnSaveInscription.disabled = true;
                btnSubInscription.disabled = true;
            }
        }

    });


    const cughMembership = {

        init: function () {
            this.bindEvents();
            this.runInitialState();
        },

        bindEvents: function () {
            document.addEventListener('change', (e) => {
                if (
                    e.target.matches('input[name="is_cugh_member"]') ||
                    e.target.matches('#cugh_membership_type') ||
                    e.target.matches('#cugh_member_institution')
                ) {
                    this.runInitialState();
                }
            });
        },

        getMembershipType: function () {
            const isCughMember = document.querySelector('input[name="is_cugh_member"]:checked');
            const membershipType = document.getElementById('cugh_membership_type');
            const institution = document.getElementById('cugh_member_institution');

            if (!isCughMember || isCughMember.value == '0') {
                return 'non-member';
            }

            if (membershipType.value === 'Institutional Member') {
                if (institution.value !== '') {
                    return 'member';
                }

                return 'non-member';
            }

            if (membershipType.value === 'Individual Member') {
                if (isMemberIndividualVerified) {
                    return 'member';
                }

                return 'non-member';
            }

            return 'non-member';
        },

        updateCategories: function (type) {
            document.querySelectorAll('.category-row').forEach(row => {
                const membership = row.dataset.membership;
                const radio = row.querySelector('input[type="radio"]');

                if (!radio) return;

                if (membership === 'all' || membership === type) {
                    row.classList.remove('d-none');
                } else {
                    row.classList.add('d-none');
                    radio.checked = false;
                }
            });

            if (typeof calculateTotalPrice === "function") {
                calculateTotalPrice();
            }
        },

        runInitialState: function () {
            const type = this.getMembershipType();
            this.updateCategories(type);
        }

    };

    // 🚀 inicializar módulo
    cughMembership.init();

    const workplaceInput = document.getElementById('inputWorkplace');
    const workplaceMembershipAlert = document.getElementById('workplace_membership_alert');
    const institutionNames = typeof memberInstitutionNames !== 'undefined' ? memberInstitutionNames : [];
    const ignoredInstitutionWords = new Set([
        'and', 'the', 'for', 'from', 'with', 'of', 'de', 'del', 'la', 'las', 'el', 'los', 'y'
    ]);
    let workplaceCheckTimer = null;

    function institutionWords(value) {
        return [...new Set(
            String(value || '')
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .toLowerCase()
                .replace(/[^a-z0-9]+/g, ' ')
                .trim()
                .split(/\s+/)
                .filter(word => word.length >= 3 && !ignoredInstitutionWords.has(word))
        )];
    }

    function checkWorkplaceMembership() {
        if (!workplaceInput || !workplaceMembershipAlert) return;

        const memberSelection = document.querySelector('input[name="is_cugh_member"]:checked');
        const workplaceWords = new Set(institutionWords(workplaceInput.value));

        if (!memberSelection || memberSelection.value !== '0' || workplaceWords.size < 2) {
            workplaceMembershipAlert.classList.add('d-none');
            workplaceMembershipAlert.textContent = '';
            return;
        }

        const matches = institutionNames.filter(name => {
            const matchingWords = institutionWords(name).filter(word => workplaceWords.has(word));
            return matchingWords.length >= 2;
        }).slice(0, 3);

        if (matches.length === 0) {
            workplaceMembershipAlert.classList.add('d-none');
            workplaceMembershipAlert.textContent = '';
            return;
        }

        workplaceMembershipAlert.textContent = `Your workplace may be a CUGH member institution: ${matches.join(', ')}. Please review the CUGH Member question above and select Yes if applicable.`;
        workplaceMembershipAlert.classList.remove('d-none');
    }

    if (workplaceInput) {
        workplaceInput.addEventListener('input', () => {
            clearTimeout(workplaceCheckTimer);
            workplaceCheckTimer = setTimeout(checkWorkplaceMembership, 400);
        });
        document.querySelectorAll('input[name="is_cugh_member"]').forEach(radio => {
            radio.addEventListener('change', checkWorkplaceMembership);
        });
        checkWorkplaceMembership();
    }


    // Eliminar espacios en tiempo real
    const noSpacesInputs = document.querySelectorAll('.no-spaces');
    noSpacesInputs.forEach(input => {
        // Eliminar espacios en tiempo real
        input.addEventListener('input', () => {
            input.value = input.value.replace(/\s/g, '');
        });

        // Opcional: prevenir espacio al teclear
        input.addEventListener('keypress', (e) => {
            if (e.key === ' ') e.preventDefault();
        });

        // Opcional: eliminar espacios al pegar
        input.addEventListener('paste', (e) => {
            e.preventDefault();
            const paste = (e.clipboardData || window.clipboardData).getData('text');
            input.value += paste.replace(/\s/g, '');
        });
    });


    //obtener valor del select option inputCountry selected value
    const selectCountry = document.getElementById('inputCountry');
    const selectedCountry = selectCountry.options[selectCountry.selectedIndex].value;
    invoiceOptions(selectedCountry);


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

    // is_cugh_member
    document.addEventListener('change', function (e) {
        if (e.target.matches('input[name="is_cugh_member"]')) {

            const isMember = e.target.value === '1';

            const membershipTypeDiv = document.getElementById('cugh_membership_type_div');
            const membershipTypeSelect = document.getElementById('cugh_membership_type');

            const institutionDiv = document.getElementById('cugh_member_institution_div');
            const institutionSelect = document.getElementById('cugh_member_institution');

            const individualInfoDiv = document.getElementById('cugh_member_individual_info_div');

            if (isMember) {
                membershipTypeDiv.classList.remove('d-none');
            } else {

                // Ocultar secciones
                membershipTypeDiv.classList.add('d-none');
                institutionDiv.classList.add('d-none');
                individualInfoDiv.classList.add('d-none');

                // Reiniciar selects
                membershipTypeSelect.selectedIndex = 0;
                institutionSelect.selectedIndex = 0;
            }
        }
    });

    //cugh_membership_type
    document.getElementById('cugh_membership_type').addEventListener('change', function () {

        const type = this.value;

        const institutionDiv = document.getElementById('cugh_member_institution_div');
        const individualInfoDiv = document.getElementById('cugh_member_individual_info_div');
        const institutionSelect = document.getElementById('cugh_member_institution');

        // Ocultar ambos por defecto
        institutionDiv.classList.add('d-none');
        individualInfoDiv.classList.add('d-none');

        // Reiniciar institución si no es institucional
        if (type !== 'Institutional Member') {
            institutionSelect.selectedIndex = 0;
        }

        // Mostrar según tipo
        if (type === 'Institutional Member') {
            institutionDiv.classList.remove('d-none');
        } else if (type === 'Individual Member') {
            individualInfoDiv.classList.remove('d-none');
        }
    });

    //Country Prices
    const countrySelect = document.getElementById('inputCountry');
    function loadPrices(countryId) {

        if (!countryId) return;

        const csrf = document.querySelector('meta[name="csrf-token"]').content;

        fetch('/category-inscriptions/prices', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf
            },
            body: JSON.stringify({
                country_id: countryId
            })
        })
        .then(res => res.json())
        .then(prices => {

            Object.keys(prices).forEach(id => {
                const price = prices[id];

                const priceSpan = document.getElementById('dc_price_' + id);
                if (priceSpan) {
                    priceSpan.innerText = price;
                }

                const radio = document.getElementById('category_' + id);
                if (radio) {
                    radio.dataset.catprice = price;
                }
            });

            calculateTotalPrice();
        });

        invoiceOptions(countryId);
    }

    // Evento change Country
    countrySelect.addEventListener('change', function () {
        loadPrices(this.value);
    });

    // Ejecutar al cargar si ya tiene valor contry
    if (countrySelect.value) {
        loadPrices(countrySelect.value);
    }


    function invoiceOptions(value){
        if(value == '176'){
            document.getElementById('dv_invoice_type_factura').classList.remove('d-none');
        }else{
            document.getElementById('dv_invoice_type_factura').classList.add('d-none');
            document.getElementById('invoice_type_boleta').checked = true;
            document.getElementById('invoice_type_factura').checked = false;
        }

        handleInvoiceTypeChange(document.querySelector('input[name="invoice_type"]:checked'));
    }

    document.querySelectorAll('.inputNumber').forEach(input => {
        // Permite solo dígitos
        input.addEventListener('input', () => {
            input.value = input.value.replace(/\D/g, '');
        });
    });

    // Función para validar campo de archivo de FilePond
    function validarArchivoFilePond(inputId, mensajeError) {
        const inputArchivo = document.getElementById(inputId);
        const filePondInstance = FilePond.find(inputArchivo);
    
        if (filePondInstance.getFiles().length === 0) {
            alert(mensajeError);
            return false;
        }
    
        return true;
    }


    function validarCamposInscription() {
        const selectedRadioCategoryInscription = document.querySelector('input[type="radio"][name="category_inscription_id"]:checked');
        const selectedRadioPaymentMethod = document.querySelector('input[type="radio"][name="payment_method"]:checked');
    
        if (selectedRadioCategoryInscription === null) {
            alert("You must select a category.");
            return false;
        }
    
        const hasDocumentFile = document.getElementById('has_document_file').value === '1';
    
        if (selectedRadioCategoryInscription.dataset.requiresDocument === '1' && !hasDocumentFile) {
            if (!validarArchivoFilePond('document_file', 'You must attach proof of category (Title, Certificate, Professional Card).')) {
                return false;
            }
        }
    
        if (selectedRadioPaymentMethod === null) {
            alert("You must select a payment method");
            return false;
        }
    
        if (selectedRadioPaymentMethod.value === 'Bank Transfer/Wire') {
            const hasVoucherFile = document.getElementById('has_voucher_file').value === '1';
            if (!hasVoucherFile && !validarArchivoFilePond('voucher_file', "You must attach proof of transfer or deposit.")) {
                return false;
            }
        }

        if(selectedRadioCategoryInscription.dataset.specialCode === '1' && document.getElementById('specialcode_verify').value === ''){
            alert('Please enter and validate a valid special fee code.');
            return false;
        }
    
        return true; // La validación pasa
    }

    function validarCamposInscriptionDraft() {
        const selectedRadioCategoryInscription = document.querySelector('input[type="radio"][name="category_inscription_id"]:checked');
    
        if (selectedRadioCategoryInscription === null) {
            alert("You must select a category.");
            return false;
        }

        const hasDocumentFile = document.getElementById('has_document_file').value === '1';
    
        if (selectedRadioCategoryInscription.dataset.requiresDocument === '1' && !hasDocumentFile) {
            if (!validarArchivoFilePond('document_file', 'Please attach supporting documentation for the selected category.')) {
                return false;
            }
        }

        if(selectedRadioCategoryInscription.dataset.specialCode === '1' && document.getElementById('specialcode_verify').value === ''){
            alert('Please enter and validate a valid special fee code.');
            const specialcode = document.getElementById('specialcode');
            specialcode.focus();
            return false;
        }
    
        return true; // La validación pasa
    }


    // Eventos
    document.querySelectorAll('input[name="invoice_type"]').forEach(radio => {

        radio.addEventListener('change', function () {
            handleInvoiceTypeChange(this);
        });

        // Ejecutar automáticamente si ya viene seleccionado
        if (radio.checked) {
            handleInvoiceTypeChange(radio);
        }
    });

    function handleInvoiceTypeChange(radio){
        const labelSocialReason = document.getElementById('lbl_invoice_social_reason');
        const labelInvoiceAddress = document.getElementById('lbl_invoice_address');
        const selectInvoiceDocument = document.getElementById('invoice_type_document');

        if (radio.value === 'Factura') {

            labelSocialReason.textContent = 'Entity Name';
            labelInvoiceAddress.textContent = 'Business Address';

            Array.from(selectInvoiceDocument.options).forEach(option => {

                // Solo RUC habilitado
                option.disabled = option.value !== 'RUC';

                // Seleccionar RUC automáticamente
                if (option.value === 'RUC') {
                    option.selected = true;
                }
            });

        } else {

            labelSocialReason.textContent = 'Full Name or entity';
            labelInvoiceAddress.textContent = 'Full Postal Address';

            // Habilitar todas menos RUC
            Array.from(selectInvoiceDocument.options).forEach(option => {
                option.disabled = option.value === 'RUC';
            });

            // Si estaba RUC seleccionado, volver a "Select..."
            if (selectInvoiceDocument.value === 'RUC') {
                selectInvoiceDocument.selectedIndex = 0;
            }
        }

    }


    document.getElementById('invoice_type_document').addEventListener('change', function () {
        document.getElementById('invoice_ruc').value = '';
    })


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

    // SECTOR
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



// Obtén todos los elementos radio y checkboxes
const categoryRadioButtons = document.querySelectorAll('input[type="radio"][name="category_inscription_id"]');
const paymentotalElement = document.getElementById('paymentotal');

// Función para calcular el precio total
function calculateTotalPrice() {
  let totalPrice = 0;
  
  // Suma los valores de los radios seleccionados
  categoryRadioButtons.forEach(radio => {
    if (radio.checked) {
      const catPrice = parseFloat(radio.getAttribute('data-catprice'));
      totalPrice += catPrice;
    }
  });
  


    if(totalPrice == 0){
         //Ocultar dv_payment_method
        document.getElementById('dv_payment_method').classList.add('d-none');
        //marcar radio payment_method_none
        document.getElementById('payment_method_none').checked = true;
        //mostrar dv_payment_method_none
        document.getElementById('dv_nopayment').classList.remove('d-none');
        //Ocultar dv_tranfer
        document.getElementById('dv_tranfer').classList.add('d-none');
        //Ocultar dv_card
        document.getElementById('dv_card').classList.add('d-none');
       
    }else{
        //Mostrar dv_payment_method
        document.getElementById('dv_payment_method').classList.remove('d-none');
        //desmarcar radio payment_method_none
        document.getElementById('payment_method_none').checked = false;
        //ocultar dv_payment_method_none
        document.getElementById('dv_nopayment').classList.add('d-none');
        
    }


  // Actualiza el elemento HTML con el precio total
  paymentotalElement.textContent = totalPrice; // Ajusta el formato según necesites
}


// Agrega un event listener para los cambios en los radios y checkboxes

categoryRadioButtons.forEach(radio => {
  radio.addEventListener('change', function (e) {
      calculateTotalPrice();
      handleCategoryRadioButtons(e.target.value);
  });
});




// Calcula el precio total inicial
calculateTotalPrice();

// Obtén los elementos del DOM
const dvDocumentFile = document.getElementById('dv_document_file');
const dvSpecialCode = document.getElementById('dv_specialcode');
const inputSpecialCode = document.getElementById('specialcode');
const txtPriceSpecialCode = document.getElementById('dc_price_5');
const btnValidateSpecialCode = document.getElementById('validate_specialcode');
const btnClearSpecialCode = document.getElementById('clear_specialcode');
const specialCodeVerify = document.getElementById('specialcode_verify');
const descriptionSpecialCode = document.getElementById('sms_valid_vc');

// Función para manejar el clic categoryRadioButtons


const selectedRadioCategory = document.querySelector('input[type="radio"][name="category_inscription_id"]:checked');
const selectedValueCategory = selectedRadioCategory ? selectedRadioCategory.value : null;

function handleCategoryRadioButtons(selectedValueCategory){
    
    if(!selectedValueCategory) return;

    if(selectedValueCategory === '3' || selectedValueCategory === '4' || selectedValueCategory === '5'){
      
        //Document file required
        dvDocumentFile.classList.remove('d-none');
        
        inputSpecialCode.value = '';
        inputSpecialCode.removeAttribute('required');
        inputSpecialCode.removeAttribute('readonly');
        txtPriceSpecialCode.textContent = '00';
        descriptionSpecialCode.textContent = '';
        specialCodeVerify.value = '';
        btnValidateSpecialCode.classList.remove('d-none');
        btnClearSpecialCode.classList.add('d-none');

    }else if(selectedValueCategory === '1' || selectedValueCategory === '2'){
        
        //Document file not required
        dvDocumentFile.classList.add('d-none');
        
        //Special code required not validation
        dvSpecialCode.classList.add('d-none');
        inputSpecialCode.value = '';
        inputSpecialCode.removeAttribute('required');
        inputSpecialCode.removeAttribute('readonly');
        txtPriceSpecialCode.textContent = '00';
        descriptionSpecialCode.textContent = '';
        specialCodeVerify.value = '';
        btnValidateSpecialCode.classList.remove('d-none');
        btnClearSpecialCode.classList.add('d-none');

      } else if(selectedValueCategory === '6'){
        
        //Document file not required
        dvDocumentFile.classList.remove('d-none');

        //Special code required validation
        dvSpecialCode.classList.remove('d-none');
        inputSpecialCode.setAttribute('required', 'required');
        inputSpecialCode.removeAttribute('readonly');
        txtPriceSpecialCode.textContent = '00';
        descriptionSpecialCode.textContent = '';
        specialCodeVerify.value = '';
        btnValidateSpecialCode.classList.remove('d-none');
        btnClearSpecialCode.classList.add('d-none');
      }else{
        
        //Document file not required
        dvDocumentFile.classList.add('d-none');

        //Special code required not validation
        dvSpecialCode.classList.add('d-none');
        inputSpecialCode.value = '';
        inputSpecialCode.removeAttribute('required');
        inputSpecialCode.removeAttribute('readonly');
        txtPriceSpecialCode.textContent = '00';
        descriptionSpecialCode.textContent = '';
        specialCodeVerify.value = '';
        btnValidateSpecialCode.classList.remove('d-none');
        btnClearSpecialCode.classList.add('d-none');
      
    }

    const radioCategory = document.getElementById('category_6');
    radioCategory.setAttribute('data-catprice', '00');

}

if(selectedValueCategory){
    handleCategoryRadioButtons(selectedValueCategory);
}

//if  clic in radio invoice if value is yes add class in dv_invoice_info
const dvInvoiceInfo = document.getElementById('dv_invoice_info');
const inputInvoice = document.querySelectorAll('input[type="radio"][name="invoice"]');
const inputInvoiceRuc = document.getElementById('invoice_ruc');
const inputInvoiceSocialReason = document.getElementById('invoice_social_reason');
const inputInvoiceAddress = document.getElementById('invoice_address');

//al escribir en inputInvoiceRuc si es RUC o DNI acepted numbers
inputInvoiceRuc.addEventListener('input', function() {
    const invoice_type_document = document.getElementById('invoice_type_document').value;
    if (invoice_type_document === 'RUC' || invoice_type_document === 'DNI') {
        this.value = this.value.replace(/[^0-9]/g, '');
    }
});


inputInvoice.forEach(radio => {
    radio.addEventListener('change', handleInvoice);
});

function handleInvoice(){
    const selectedValueInvoice = document.querySelector('input[type="radio"][name="invoice"]:checked').value;
    if(selectedValueInvoice === 'yes'){
        dvInvoiceInfo.classList.remove('d-none');
        inputInvoiceRuc.setAttribute('required', 'required');
        inputInvoiceSocialReason.setAttribute('required', 'required');
        inputInvoiceAddress.setAttribute('required', 'required');
    }else{
        dvInvoiceInfo.classList.add('d-none');
        inputInvoiceRuc.removeAttribute('required');
        inputInvoiceSocialReason.removeAttribute('required');
        inputInvoiceAddress.removeAttribute('required');
    }
}


document.getElementById('cugh_member_institution').addEventListener('change', function() {

    let institutionId = this.value;
    let countrySelect = document.getElementById('inputCountry');

    // Resetear select
    countrySelect.innerHTML = '<option value="">Select country...</option>';

    let url = institutionId 
    ? `/countries-by-institution/${institutionId}` 
    : `/countries-by-institution`;


    fetch(url)
        .then(response => response.json())
        .then(data => {

            data.forEach(country => {
                let option = document.createElement('option');
                option.value = country.id;
                option.textContent = country.name;
                countrySelect.appendChild(option);
            });

        })
        .catch(error => {
            console.error('Error:', error);
        });

});

//validate specialcode when click validate_specialcode button
btnValidateSpecialCode.addEventListener('click', function(){

  //valida si el campo esta vacio
    if(inputSpecialCode.value === ''){
        alert('Enter a special code.');
        return false;
    }

  const radioCategory = document.getElementById('category_6');
    //verifica si el existe via ajax javascript
  const url = baseurl + '/validate-specialcode';
  const code = inputSpecialCode.value;

  const xhr = new XMLHttpRequest();
  xhr.open('POST', url, true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded'); // Configura el tipo de contenido

  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4) {
      if (xhr.status === 200) {
        const response = JSON.parse(xhr.responseText);
        if (response.success) {
          txtPriceSpecialCode.textContent = Math.floor(response.price);
          inputSpecialCode.setAttribute('readonly', 'readonly');
          descriptionSpecialCode.innerHTML = '<span class="text-success">'+response.message+'</span>'
          btnClearSpecialCode.classList.remove('d-none');
          btnValidateSpecialCode.classList.add('d-none');
          specialCodeVerify.value = 'valid';
          radioCategory.setAttribute('data-catprice', Math.floor(response.price));
          

        } else {
          descriptionSpecialCode.innerHTML = '<span class="text-danger">'+response.message+'</span>';
          txtPriceSpecialCode.textContent = '00';
          inputSpecialCode.removeAttribute('readonly');
          specialCodeVerify.value = '';
          radioCategory.setAttribute('data-catprice', '0.00');
        }

        calculateTotalPrice();

      } else {
        alert('Error en la solicitud.');
      }
    }
  };

  // Configura los datos a enviar en la solicitud POST
  const token = $('meta[name="csrf-token"]').attr('content');
  const params = `code=${code}&_token=${token}`;
  
  xhr.send(params);

    
});

btnClearSpecialCode.addEventListener('click', function(){
    inputSpecialCode.value = '';
    txtPriceSpecialCode.textContent = '00';
    inputSpecialCode.removeAttribute('readonly');
    descriptionSpecialCode.textContent = '';
    btnClearSpecialCode.classList.add('d-none');
    btnValidateSpecialCode.classList.remove('d-none');
    specialCodeVerify.value = '';
    calculateTotalPrice();
});

const inputPaymentMethod = document.querySelectorAll('input[type="radio"][name="payment_method"]');
const dvTranfer = document.getElementById('dv_tranfer');
const dvCard = document.getElementById('dv_card');

inputPaymentMethod.forEach(radio => {
    radio.addEventListener('change', handlePaymentMethod);
});

function handlePaymentMethod(){
    const selectedPaymentMethod = document.querySelector('input[type="radio"][name="payment_method"]:checked');
    if (!selectedPaymentMethod) return;

    const selectedValuePaymentMethod = selectedPaymentMethod.value;
    if(selectedValuePaymentMethod === 'Bank Transfer/Wire'){
        dvTranfer.classList.remove('d-none');
        dvCard.classList.add('d-none');
    }else if(selectedValuePaymentMethod === 'Credit/Debit Card'){
        dvTranfer.classList.add('d-none');
        dvCard.classList.remove('d-none');
    }else{
        dvTranfer.classList.add('d-none');
        dvCard.classList.add('d-none');
    }
}

handlePaymentMethod();

const inputDocumentFile = document.getElementById('document_file');
let uploadsInProgress = 0;

function setUploadState(isStarting) {
    uploadsInProgress = Math.max(0, uploadsInProgress + (isStarting ? 1 : -1));
    const isUploading = uploadsInProgress > 0;

    if (btnSubInscription) {
        btnSubInscription.disabled = isUploading;
        btnSubInscription.textContent = isUploading ? 'Uploading file... Please wait.' : 'Register Now';
    }

    if (btnSaveInscription) {
        btnSaveInscription.disabled = isUploading;
        btnSaveInscription.textContent = isUploading ? 'Uploading file... Please wait.' : 'Save Draft';
    }
}

// Configure the upload endpoint before creating any FilePond instance.
const filePondPlugins = [FilePondPluginFileValidateType];
if (typeof FilePondPluginImagePreview !== 'undefined') {
    filePondPlugins.push(FilePondPluginImagePreview);
}
FilePond.registerPlugin(...filePondPlugins);

const currentDocumentFile = typeof existingDocumentFile !== 'undefined' ? existingDocumentFile : null;
const currentVoucherFile = typeof existingVoucherFile !== 'undefined' ? existingVoucherFile : null;
const inscriptionIdForFiles = typeof currentInscriptionId !== 'undefined' ? currentInscriptionId : null;

FilePond.setOptions({
    server: {
        process: {
            url: baseurl + '/upload',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        },
        load: (source, load, error, progress, abort) => {
            const controller = new AbortController();

            fetch(source, { signal: controller.signal })
                .then(response => {
                    if (!response.ok) throw new Error('Unable to load the existing file.');
                    return response.blob();
                })
                .then(load)
                .catch(loadError => {
                    if (loadError.name !== 'AbortError') error(loadError.message);
                });

            return {
                abort: () => {
                    controller.abort();
                    abort();
                }
            };
        }
    }
});

function existingFileOptions(file) {
    if (!file) return [];

    return [{
        source: file.url,
        options: {
            type: 'local',
            file: { name: file.name }
        }
    }];
}

function hasAllowedRegistrationExtension(file) {
    if (file.origin === FilePond.FileOrigin.LOCAL) return true;

    const extension = String(file.filename || '').split('.').pop().toLowerCase();
    const allowed = ['pdf', 'jpg', 'jpeg', 'png'].includes(extension);

    if (!allowed) {
        alert('Only PDF, JPG, JPEG, and PNG files are allowed.');
    }

    return allowed;
}

function detectRegistrationFileType(source, browserType) {
    const filename = typeof source === 'string' ? source : (source.name || '');
    const extension = filename.split('?')[0].split('.').pop().toLowerCase();
    const mimeTypes = {
        pdf: 'application/pdf',
        jpg: 'image/jpeg',
        jpeg: 'image/jpeg',
        png: 'image/png'
    };

    return Promise.resolve(mimeTypes[extension] || browserType);
}

async function removeStoredFile(file, type) {
    if (file.origin !== FilePond.FileOrigin.LOCAL) return true;

    const label = type === 'document' ? 'supporting document' : 'proof of transfer';
    if (!confirm(`Are you sure you want to delete this ${label}? This action cannot be undone.`)) {
        return false;
    }

    if (!inscriptionIdForFiles) return false;

    const response = await fetch(`/inscriptions/${inscriptionIdForFiles}/delete-${type}-file`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    });
    const data = await response.json();

    if (!response.ok || !data.success) {
        alert(data.message || `The ${label} could not be deleted.`);
        return false;
    }

    const hiddenInput = document.getElementById(type === 'document' ? 'has_document_file' : 'has_voucher_file');
    const existingCard = document.getElementById(type === 'document' ? 'card_document_file' : 'card_voucher_file');
    if (hiddenInput) hiddenInput.value = '0';
    if (existingCard) existingCard.classList.add('d-none');

    return true;
}

const pondDocument = FilePond.create(inputDocumentFile, {
    files: existingFileOptions(currentDocumentFile),
    allowRemove: true,
    acceptedFileTypes: ['application/pdf', 'image/jpeg', 'image/png'],
    fileValidateTypeDetectType: detectRegistrationFileType,
    beforeAddFile: hasAllowedRegistrationExtension,
    beforeRemoveFile: file => removeStoredFile(file, 'document'),
    onprocessfilestart: () => setUploadState(true),
    onprocessfile: (error) => {
        setUploadState(false);
        if (error) {
            alert('The supporting document could not be uploaded. Please use a PDF, JPG, JPEG, or PNG file up to 10 MB.');
        }
    }
});

const inputVoucherFile  = document.getElementById("voucher_file");
const pondVoucher = FilePond.create(inputVoucherFile, {
    files: existingFileOptions(currentVoucherFile),
    allowRemove: true,
    acceptedFileTypes: ['application/pdf', 'image/jpeg', 'image/png'],
    fileValidateTypeDetectType: detectRegistrationFileType,
    beforeAddFile: hasAllowedRegistrationExtension,
    beforeRemoveFile: file => removeStoredFile(file, 'voucher'),
    onprocessfilestart: () => setUploadState(true),
    onprocessfile: (error) => {
        setUploadState(false);
        if (error) {
            alert('The proof of transfer could not be uploaded. Please use a PDF, JPG, JPEG, or PNG file up to 10 MB.');
        }
    }
});


//Delete File Document btn_delete_document_file
const btnDeleteDocumentFile = document.getElementById('btn_delete_document_file');
if (btnDeleteDocumentFile) {
    if (currentDocumentFile) btnDeleteDocumentFile.classList.add('d-none');
    btnDeleteDocumentFile.addEventListener('click', function () {
        
        if (!confirm('Are you sure you want to delete this document? This action cannot be undone.')) {
            return;
        }

        //data-id get value
        const id = this.getAttribute('data-id');

        fetch(`/inscriptions/${id}/delete-document-file`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                id: id
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                pondDocument.setOptions({ allowRemove: true });
                pondDocument.removeFile();
                const card_document_file = document.getElementById('card_document_file');
                card_document_file.classList.add('d-none');
                document.getElementById('has_document_file').value = '0';
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error(error);
            alert('An error occurred while deleting the document.');
        });



    });
}

const btnDeleteVoucherFile = document.getElementById('btn_delete_voucher_file');
if (btnDeleteVoucherFile) {
    if (currentVoucherFile) btnDeleteVoucherFile.classList.add('d-none');
    btnDeleteVoucherFile.addEventListener('click', function () {
        if (!confirm('Are you sure you want to delete this proof of transfer? This action cannot be undone.')) {
            return;
        }

        const id = this.getAttribute('data-id');

        fetch(`/inscriptions/${id}/delete-voucher-file`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json().then(data => ({ ok: response.ok, data })))
        .then(result => {
            if (!result.ok || !result.data.success) {
                throw new Error(result.data.message || 'The proof of transfer could not be deleted.');
            }

            pondVoucher.setOptions({ allowRemove: true });
            pondVoucher.removeFile();
            document.getElementById('card_voucher_file').classList.add('d-none');
            document.getElementById('has_voucher_file').value = '0';
        })
        .catch(error => alert(error.message));
    });
}


