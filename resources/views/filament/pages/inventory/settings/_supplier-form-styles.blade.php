<style>
    .supplier-form {
        width: 100%;
        max-width: 862px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        background: #ffffff;
        padding: 28px 26px 26px;
    }

    .supplier-form__grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        column-gap: 18px;
        row-gap: 18px;
    }

    .supplier-form__field {
        display: flex;
        min-width: 0;
        flex-direction: column;
        gap: 8px;
        color: #001b33;
        font-size: 16px;
        font-weight: 500;
        line-height: 1.25;
    }

    .supplier-form__field--wide,
    .supplier-form__check {
        grid-column: 1 / -1;
    }

    .supplier-form__required {
        color: #dc2626;
    }

    .supplier-form__control {
        width: 100%;
        min-width: 0;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        background: #ffffff;
        padding: 8px 14px;
        color: #001b33;
        font-size: 16px;
        line-height: 1.4;
        outline: none;
    }

    input.supplier-form__control {
        height: 44px;
    }

    textarea.supplier-form__control {
        min-height: 64px;
        resize: vertical;
    }

    .supplier-form__combo {
        position: relative;
    }

    .supplier-form__combo-wrap {
        position: relative;
        min-width: 0;
    }

    .supplier-form__combo-input {
        padding-right: 40px;
    }

    .supplier-form__combo-wrap::after {
        position: absolute;
        right: 14px;
        top: 50%;
        width: 8px;
        height: 8px;
        border-right: 2px solid #64748b;
        border-bottom: 2px solid #64748b;
        content: "";
        pointer-events: none;
        transform: translateY(-65%) rotate(45deg);
    }

    .supplier-form__combo-menu {
        position: absolute;
        left: 0;
        right: 0;
        top: calc(100% + 6px);
        z-index: 30;
        max-height: 220px;
        overflow-y: auto;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        background: #ffffff;
        box-shadow: 0 16px 32px rgba(15, 23, 42, 0.14);
    }

    .supplier-form__combo-option {
        display: flex;
        width: 100%;
        align-items: center;
        border: 0;
        background: transparent;
        padding: 10px 14px;
        color: #001b33;
        font-size: 15px;
        text-align: left;
    }

    .supplier-form__combo-option:hover,
    .supplier-form__combo-option--selected {
        background: #f1f5f9;
    }

    .supplier-form__combo-empty {
        padding: 10px 14px;
        color: #64748b;
        font-size: 14px;
    }

    .supplier-form__control:focus {
        border-color: #173c63;
        box-shadow: 0 0 0 1px #173c63;
    }

    .supplier-form__control::placeholder {
        color: #94a3b8;
    }

    .supplier-form__error {
        color: #dc2626;
        font-size: 12px;
        line-height: 1.35;
    }

    .supplier-form__check {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #001b33;
        font-size: 16px;
        font-weight: 400;
    }

    .supplier-form__check input {
        width: 15px;
        height: 15px;
        accent-color: #007062;
    }

    .supplier-form__actions {
        display: flex;
        gap: 14px;
        margin-top: 24px;
    }

    .supplier-form__button,
    .supplier-form__cancel {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 106px;
        height: 44px;
        border-radius: 8px;
        padding: 0 26px;
        font-size: 16px;
        font-weight: 500;
        line-height: 1;
    }

    .supplier-form__button {
        border: 1px solid #007062;
        background: #007062;
        color: #ffffff;
    }

    .supplier-form__cancel {
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #001b33;
        text-decoration: none;
    }

    @media (max-width: 767px) {
        .supplier-form {
            max-width: none;
            padding: 22px 18px;
        }

        .supplier-form__grid {
            grid-template-columns: 1fr;
        }

        .supplier-form__field--wide,
        .supplier-form__check {
            grid-column: auto;
        }

        .supplier-form__actions {
            flex-direction: column;
        }

        .supplier-form__button,
        .supplier-form__cancel {
            width: 100%;
        }
    }

    .dark .supplier-form {
        border-color: #334155;
        background: #111827;
    }

    .dark .supplier-form__field,
    .dark .supplier-form__check {
        color: #ffffff;
    }

    .dark .supplier-form__control {
        border-color: #475569;
        background: #1f2937;
        color: #ffffff;
    }

    .dark .supplier-form__combo-menu {
        border-color: #475569;
        background: #111827;
    }

    .dark .supplier-form__combo-option {
        color: #ffffff;
    }

    .dark .supplier-form__combo-option:hover,
    .dark .supplier-form__combo-option--selected {
        background: #1f2937;
    }

    .dark .supplier-form__combo-empty {
        color: #94a3b8;
    }

    .dark .supplier-form__cancel {
        border-color: #475569;
        background: #111827;
        color: #ffffff;
    }
</style>
