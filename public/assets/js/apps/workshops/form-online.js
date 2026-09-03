document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('workshopForm');
    if (!form) return;
    const errors = document.getElementById('workshopClientErrors');
    const button = form.querySelector('[type="submit"]');
    const buttonText = button.textContent;
    let submitting = false;

    function showError(message) {
        errors.textContent = message;
        errors.classList.remove('d-none');
        errors.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    function updatePaymentFields() {
        const differentContact = form.querySelector('[name="payment_lead_same"]:checked')?.value === 'No';
        const fields = document.getElementById('paymentLeadFields');
        fields.style.display = differentContact ? 'block' : 'none';
        fields.querySelectorAll('input').forEach(function (input) {
            input.required = differentContact;
            input.disabled = !differentContact;
        });
    }
    form.querySelectorAll('[name="payment_lead_same"]').forEach(function (radio) {
        radio.addEventListener('change', updatePaymentFields);
    });
    updatePaymentFields();

    form.querySelectorAll('.word-limit').forEach(function (textarea) {
        function countWords() {
            const count = textarea.value.trim().split(/\s+/).filter(Boolean).length;
            const maximum = Number(textarea.dataset.maxWords);
            const counter = textarea.parentElement.querySelector('.word-count');
            counter.textContent = count + ' / ' + maximum + ' words';
            counter.classList.toggle('text-danger', count > maximum);
            counter.classList.toggle('text-muted', count <= maximum);
            textarea.setCustomValidity(count > maximum ? 'The workshop description must not exceed 200 words.' : '');
        }
        textarea.addEventListener('input', countWords);
        countWords();
    });

    function validateDuration() {
        const slot = form.elements.time_slot.value;
        const duration = form.querySelector('[name="day_length"]:checked')?.value;
        const mismatch = slot && duration && ((slot === 'Full Day, 9am-4pm') !== (duration === 'Full Day'));
        form.elements.time_slot.setCustomValidity(mismatch ? 'The selected duration must match the preferred time slot.' : '');
    }
    form.querySelectorAll('[name="time_slot"], [name="day_length"]').forEach(function (input) {
        input.addEventListener('change', validateDuration);
    });
    validateDuration();

    const canvas = document.getElementById('signatureCanvas');
    let signaturePad = null;
    let previousWidth = 0;
    let previousHeight = 0;
    if (typeof SignaturePad === 'function') {
        signaturePad = new SignaturePad(canvas, {
            backgroundColor: 'rgb(255, 255, 255)', penColor: 'rgb(0, 0, 0)',
            minWidth: 1, maxWidth: 3, velocityFilterWeight: 0.7
        });
        function resizeCanvas() {
            const width = canvas.offsetWidth;
            const height = canvas.offsetHeight;
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            if (!width || !height || (canvas.width === Math.round(width * ratio) && canvas.height === Math.round(height * ratio))) return;
            const data = signaturePad.toData();
            if (previousWidth && previousHeight) {
                data.forEach(function (group) {
                    group.points.forEach(function (point) {
                        point.x *= width / previousWidth;
                        point.y *= height / previousHeight;
                    });
                });
            }
            canvas.width = Math.round(width * ratio);
            canvas.height = Math.round(height * ratio);
            canvas.getContext('2d').scale(ratio, ratio);
            signaturePad.clear();
            if (data.length) signaturePad.fromData(data);
            previousWidth = width;
            previousHeight = height;
        }
        window.addEventListener('resize', resizeCanvas);
        resizeCanvas();
    } else {
        showError('The signature tool could not load. Please reload the page before submitting.');
    }

    document.getElementById('clearSig').addEventListener('click', function () {
        if (signaturePad) signaturePad.clear();
        document.getElementById('signatureInput').value = '';
    });
    form.addEventListener('submit', function (event) {
        if (submitting) { event.preventDefault(); return; }
        if (!signaturePad || signaturePad.isEmpty()) {
            event.preventDefault();
            showError('Please provide a signature before submitting.');
            return;
        }
        const signature = signaturePad.toDataURL('image/png');
        if (signature.length > 1398126) {
            event.preventDefault();
            showError('The signature is too large. Please clear it and sign again (maximum 1 MB).');
            return;
        }
        document.getElementById('signatureInput').value = signature;
        submitting = true;
        button.disabled = true;
        button.textContent = 'Submitting...';
        errors.classList.add('d-none');
    });
    window.addEventListener('pageshow', function () {
        submitting = false;
        button.disabled = false;
        button.textContent = buttonText;
        updatePaymentFields();
    });
});
