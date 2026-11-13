document.addEventListener('DOMContentLoaded', function () {
    const invoiceRequestForm = document.getElementById('invoiceRequestForm');
    if (invoiceRequestForm) {
        invoiceRequestForm.addEventListener('submit', function () {
            const button = invoiceRequestForm.querySelector('button[type="submit"]');
            button.disabled = true;
            invoiceRequestForm.querySelector('.registration-invoice-label').textContent = 'Sending...';
        });
    }


    //print ficha inscripcion with printjs and html2canvas
    document
    .querySelector(".btnprintficha")
    .addEventListener("click", function (event) {

        event.preventDefault();

        const elementToCapture = document.querySelector(".ficha-inscripcion");

        const ignoreElements = function (element) {
            // Exclude controls from the printed registration.
            return element.classList.contains("actionstatus") || element.classList.contains("btnprintficha") || element.classList.contains("btneditinsc") || element.classList.contains("registration-note-trigger") || element.classList.contains("registration-invoice-form");

        };

        html2canvas(elementToCapture, {
            ignoreElements: ignoreElements,
            onclone: function (clonedDocument) {
                const answers = clonedDocument.getElementById("registrationAnswersPanel");
                if (answers) {
                    answers.classList.add("show");
                    answers.style.height = "auto";
                }
            },
        }).then(function (canvas) {
            const image = canvas.toDataURL("image/png");

            printJS({
                printable: image,
                type: "image",
                onPrintDialogClose: function () {
                    // Aquí puedes realizar acciones adicionales después de que se cierra el diálogo de impresión
                },
            });
        });
    });



});





