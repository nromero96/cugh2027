document.addEventListener("DOMContentLoaded", function () {
    const formInscription = document.getElementById("formInscription");
    var btnSubInscription = document.getElementById("btnSubInscription");
    formInscription.addEventListener("submit", function (event) {
        btnSubInscription.disabled = true;
        // Realiza la validación personalizada aquí
        if (!validarCamposInscription()) {
            event.preventDefault(); // Detiene el envío del formulario si la validación falla
            btnSubInscription.disabled = false;
        }
    });

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


    //Change Country
    document.getElementById('inputCountry').addEventListener('change', function () {

        const csrf = document.querySelector('meta[name="csrf-token"]').content;

        fetch('/category-inscriptions/prices', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf
            },
            body: JSON.stringify({
                country_id: this.value
            })
        })
        .then(res => res.json())
        .then(prices => {

            Object.keys(prices).forEach(id => {
                const price = prices[id];

                // actualizar span
                const priceSpan = document.getElementById('dc_price_' + id);
                if (priceSpan) {
                    priceSpan.innerText = price;
                }

                // actualizar radio data
                const radio = document.getElementById('category_' + id);
                if (radio) {
                    radio.dataset.catprice = price;
                }
            });

            calculateTotalPrice();
        });


        invoiceOptions(this.value);

    });


    function invoiceOptions(value){
        if(value == '176'){
            document.getElementById('dv_invoice_type_factura').classList.remove('d-none');
        }else{
            document.getElementById('dv_invoice_type_factura').classList.add('d-none');
            document.getElementById('invoice_type_boleta').checked = true;
            document.getElementById('invoice_type_factura').checked = false;
        }
    }

    document.querySelectorAll('.inputNumber').forEach(input => {
        // Permite solo dígitos
        input.addEventListener('input', () => {
            input.value = input.value.replace(/\D/g, '');
        });
    });


    function validarCamposInscription() {
        const selectedRadioCategoryInscription = document.querySelector('input[type="radio"][name="category_inscription_id"]:checked');
        const selectedRadioPaymentMethod = document.querySelector('input[type="radio"][name="payment_method"]:checked');
    
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
    
        if (selectedRadioCategoryInscription === null) {
            alert("You must select a category.");
            return false;
        }
    
        const categoriasPermitidas = ['3', '4', '5','6'];
    
        if (categoriasPermitidas.includes(selectedRadioCategoryInscription.value)) {
            if (!validarArchivoFilePond('document_file', "You must attach proof of category (Title, Certificate, Professional Card).")) {
                return false;
            }
        }
    
        if (selectedRadioPaymentMethod === null) {
            alert("You must select a payment method");
            return false;
        }
    
        if (selectedRadioPaymentMethod.value === 'Bank Transfer/Wire') {
            if (!validarArchivoFilePond('voucher_file', "You must attach proof of transfer or deposit.")) {
                return false;
            }
        }

        if(selectedRadioCategoryInscription.value === '6' && document.getElementById('specialcode_verify').value === ''){
            alert('You must validate the special fee code.');
            return false;
        }
    
        return true; // La validación pasa
    }
    

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
  radio.addEventListener('change', calculateTotalPrice);
  radio.addEventListener('change', handleCategoryRadioButtons);
});


// Calcula el precio total inicial
calculateTotalPrice();

// Obtén los elementos del DOM
const dvDocumentFile = document.getElementById('dv_document_file');
const inputDocumentFile = document.getElementById('document_file');
const dvSpecialCode = document.getElementById('dv_specialcode');
const inputSpecialCode = document.getElementById('specialcode');
const txtPriceSpecialCode = document.getElementById('dc_price_5');
const btnValidateSpecialCode = document.getElementById('validate_specialcode');
const btnClearSpecialCode = document.getElementById('clear_specialcode');
const specialCodeVerify = document.getElementById('specialcode_verify');
const descriptionSpecialCode = document.getElementById('sms_valid_vc');

// Función para manejar el clic categoryRadioButtons
function handleCategoryRadioButtons(){
    
    const selectedValueCategory = document.querySelector('input[type="radio"][name="category_inscription_id"]:checked').value;

    if(selectedValueCategory === '3' || selectedValueCategory === '4' || selectedValueCategory === '5'){
      
      //Document file required
      dvDocumentFile.classList.remove('d-none');
      inputDocumentFile.setAttribute('required', 'required');

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

    }else if(selectedValueCategory === '1' || selectedValueCategory === '2'){
        
        //Document file not required
        dvDocumentFile.classList.add('d-none');
        inputDocumentFile.removeAttribute('required');
        
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
        inputDocumentFile.setAttribute('required', 'required');

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
        inputDocumentFile.removeAttribute('required');

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

//if  clic in radio invoice if value is yes add class in dv_invoice_info
const dvInvoiceInfo = document.getElementById('dv_invoice_info');
const inputInvoice = document.querySelectorAll('input[type="radio"][name="invoice"]');
const inputInvoiceRuc = document.getElementById('invoice_ruc');
const inputInvoiceSocialReason = document.getElementById('invoice_social_reason');
const inputInvoiceAddress = document.getElementById('invoice_address');

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
    const selectedValuePaymentMethod = document.querySelector('input[type="radio"][name="payment_method"]:checked').value;
    if(selectedValuePaymentMethod === 'Bank Transfer/Wire'){
        dvTranfer.classList.remove('d-none');
        dvCard.classList.add('d-none');
    }else{
        dvTranfer.classList.add('d-none');
        dvCard.classList.remove('d-none');
    }
}

const inputIds = ["document_file", "voucher_file"];

inputIds.forEach((inputId) => {
  const inputElement = document.getElementById(inputId);
  FilePond.create(inputElement, {
      
    
      onaddfilestart: () => {
        btnSubInscription.disabled = true;
        btnSubInscription.textContent = 'Uploading file... Please wait.';
      },
      onprocessfile: () => {
        btnSubInscription.disabled = false,
        btnSubInscription.textContent = 'Register Now';
      }
  });
});

FilePond.setOptions({
  server: {
      url: baseurl + '/upload',
      headers: {
          'x-csrf-token': $('meta[name="csrf-token"]').attr('content'),
      },
  },
});