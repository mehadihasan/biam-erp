<style>
    .shared-user-menu {
        position: relative;
        display: inline-flex;
        z-index: 70;
    }

    .shared-user-menu__trigger {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 42px;
        height: 42px;
        border: 0;
        border-radius: 999px;
        background: #09090b;
        color: #ffffff;
        font-size: 17px;
        font-weight: 700;
        line-height: 1;
        cursor: pointer;
        list-style: none;
    }

    .shared-user-menu__trigger::-webkit-details-marker {
        display: none;
    }

    .shared-user-menu__panel {
        position: absolute;
        top: calc(100% + 10px);
        right: 0;
        width: 270px;
        overflow: hidden;
        border: 1px solid #d7dde6;
        border-radius: 9px;
        background: #ffffff;
        color: #2f3440;
        box-shadow: 0 14px 36px rgba(15, 23, 42, 0.18);
    }

    .shared-user-menu__identity,
    .shared-user-menu__logout button {
        display: flex;
        align-items: center;
        gap: 12px;
        width: 100%;
        min-height: 50px;
        padding: 0 16px;
        color: #3b3f48;
        font-size: 16px;
        font-weight: 600;
    }

    .shared-user-menu__identity-icon,
    .shared-user-menu__logout svg {
        width: 20px;
        height: 20px;
        color: #9ca3af;
        flex: 0 0 20px;
    }

    .shared-user-menu__themes {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 8px;
        border-top: 1px solid #edf0f4;
        border-bottom: 1px solid #edf0f4;
        padding: 7px;
    }

    .shared-user-menu__themes button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        height: 43px;
        border: 0;
        border-radius: 7px;
        background: transparent;
        color: #9ca3af;
        cursor: pointer;
    }

    .shared-user-menu__themes button.is-active,
    .shared-user-menu__themes button:hover {
        background: #f8fafc;
        color: #ff9500;
    }

    .shared-user-menu__themes svg {
        width: 21px;
        height: 21px;
    }

    .shared-user-menu__logout {
        margin: 0;
    }

    .shared-user-menu__logout button {
        border: 0;
        background: #ffffff;
        text-align: left;
        cursor: pointer;
    }

    .shared-user-menu__logout button:hover {
        background: #f8fafc;
    }

    @media (max-width: 520px) {
        .shared-user-menu__panel {
            width: min(270px, calc(100vw - 24px));
        }
    }
</style>
