document.addEventListener('DOMContentLoaded', () => {
    const autofillBtn = document.querySelector('[data-bcs-autofill]');
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

});
