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

    document.querySelectorAll('[data-bcs-date-field]').forEach((field) => {
        const input = field.querySelector('[data-bcs-date-input]');

        if (!input) {
            return;
        }

        const openPicker = () => {
            input.focus();

            if (typeof input.showPicker === 'function') {
                try {
                    input.showPicker();
                } catch (error) {
                    // Some browsers throw if the native picker is already opening.
                }

                return;
            }
        };

        field.addEventListener('click', () => {
            openPicker();
        });

        field.addEventListener('keydown', (event) => {
            if (event.key !== 'Enter' && event.key !== ' ') {
                return;
            }

            event.preventDefault();
            openPicker();
        });
    });
});
