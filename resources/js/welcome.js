document.addEventListener('DOMContentLoaded', () => {
    if (document.querySelector('.modal--open')) {
        document.body.classList.add('modal-locked');
    }

    const passwordInput = document.getElementById('password');
    const toggleBtn = document.querySelector('.toggle-password');

    if (passwordInput && toggleBtn) {
        toggleBtn.addEventListener('click', () => {
            const showing = passwordInput.type === 'password';
            passwordInput.type = showing ? 'text' : 'password';
            toggleBtn.setAttribute(
                'aria-label',
                showing ? toggleBtn.dataset.visibleLabel : toggleBtn.dataset.hiddenLabel
            );
        });
    }

    const autofillBtn = document.querySelector('[data-welcome-cadre-autofill]');
    const cadreInput = document.querySelector('#cadre_reference');

    if (autofillBtn && cadreInput) {
        const demoCadre = autofillBtn.getAttribute('data-demo-cadre') || '';
        autofillBtn.addEventListener('click', (e) => {
            e.preventDefault();
            cadreInput.value = demoCadre;
            cadreInput.dispatchEvent(new Event('input', { bubbles: true }));
            cadreInput.focus();
        });
    }

    const guestAutofill = document.querySelector('[data-guest-demo-autofill]');
    const guestRefInput = document.querySelector('#guest_cadre_reference');

    if (guestAutofill && guestRefInput) {
        const demoRef = guestAutofill.getAttribute('data-demo-ref') || '';
        guestAutofill.addEventListener('click', (e) => {
            e.preventDefault();
            guestRefInput.value = demoRef;
            guestRefInput.dispatchEvent(new Event('input', { bubbles: true }));
            guestRefInput.focus();
        });
    }

    const fileInput = document.querySelector('#guest_application_scan');
    const fileNameEl = document.querySelector('[data-guest-upload-filename]');

    if (fileInput && fileNameEl) {
        fileInput.addEventListener('change', () => {
            const file = fileInput.files && fileInput.files[0];
            if (file) {
                fileNameEl.textContent = file.name;
                fileNameEl.hidden = false;
            } else {
                fileNameEl.textContent = '';
                fileNameEl.hidden = true;
            }
        });
    }

    const cadreOtpInput = document.querySelector('#cadre_otp');
    if (cadreOtpInput && document.getElementById('cadre-otp-modal')?.classList.contains('modal--open')) {
        cadreOtpInput.focus();
    }

    const guestOtpInput = document.querySelector('#guest_otp');
    if (guestOtpInput && document.getElementById('guest-otp-modal')?.classList.contains('modal--open')) {
        guestOtpInput.focus();
    }
});
