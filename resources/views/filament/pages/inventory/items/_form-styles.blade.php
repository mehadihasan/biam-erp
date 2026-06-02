<style>
    .inventory-item-form {
        width: 100%;
        max-width: 864px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        background: #ffffff;
        padding: 26px 28px 28px;
    }

    .inventory-item-form__grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        column-gap: 28px;
        row-gap: 20px;
    }

    .inventory-item-form__field {
        display: flex;
        min-width: 0;
        flex-direction: column;
        gap: 8px;
        color: #001b33;
        font-size: 16px;
        font-weight: 500;
        line-height: 1.25;
    }

    .inventory-item-form__field--wide,
    .inventory-item-form__check {
        grid-column: 1 / -1;
    }

    .inventory-item-form__required {
        color: #dc2626;
    }

    .inventory-item-form__control {
        width: 100%;
        min-width: 0;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        background-color: #ffffff;
        padding: 8px 16px;
        color: #001b33;
        font-size: 16px;
        line-height: 1.4;
        outline: none;
    }

    input.inventory-item-form__control,
    select.inventory-item-form__control {
        height: 42px;
    }

    textarea.inventory-item-form__control {
        min-height: 88px;
        resize: vertical;
    }

    .inventory-item-form__control:focus {
        border-color: #173c63;
        box-shadow: 0 0 0 1px #173c63;
    }

    .inventory-item-form__control::placeholder {
        color: #94a3b8;
    }

    .inventory-item-form__error {
        color: #dc2626;
        font-size: 12px;
        line-height: 1.35;
    }

    .inventory-item-form__check {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #001b33;
        font-size: 16px;
        font-weight: 400;
    }

    .inventory-item-form__check input {
        width: 15px;
        height: 15px;
        accent-color: #0f8f80;
    }

    .inventory-item-form__actions {
        display: flex;
        gap: 14px;
        margin-top: 26px;
    }

    .inventory-item-form__button,
    .inventory-item-form__cancel {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 106px;
        height: 42px;
        border-radius: 8px;
        padding: 0 26px;
        font-size: 16px;
        font-weight: 500;
        line-height: 1;
    }

    .inventory-item-form__button {
        border: 1px solid #007f6d;
        background: #007f6d;
        color: #ffffff;
    }

    .inventory-item-form__cancel {
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #001b33;
        text-decoration: none;
    }

    @media (max-width: 767px) {
        .inventory-item-form {
            max-width: none;
            padding: 22px 18px;
        }

        .inventory-item-form__grid {
            grid-template-columns: 1fr;
            row-gap: 18px;
        }

        .inventory-item-form__field--wide,
        .inventory-item-form__check {
            grid-column: auto;
        }

        .inventory-item-form__actions {
            flex-direction: column;
        }

        .inventory-item-form__button,
        .inventory-item-form__cancel {
            width: 100%;
        }
    }

    .dark .inventory-item-form {
        border-color: #334155;
        background: #111827;
    }

    .dark .inventory-item-form__field,
    .dark .inventory-item-form__check {
        color: #ffffff;
    }

    .dark .inventory-item-form__control {
        border-color: #475569;
        background-color: #1f2937;
        color: #ffffff;
    }

    .dark .inventory-item-form__cancel {
        border-color: #475569;
        background: #111827;
        color: #ffffff;
    }
</style>
