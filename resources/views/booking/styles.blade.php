<style>
    :root {
        --color-primary: #1b6e8f;
        --color-primary-soft: #e6f5fb;
        --color-success: #2f855a;
        --color-danger: #bd2d2d;
        --color-warning: #cd7d1f;
        --color-text: #1f2a37;
        --color-muted: #5c6a7a;
        --color-border: #d9e1e8;
        --color-bg: #f4f8fb;
        --color-card: #ffffff;
        --shadow-soft: 0 12px 24px rgba(11, 56, 83, 0.08);
    }

    * {
        box-sizing: border-box;
    }

    body {
        margin: 0;
        font-family: "Tajawal", sans-serif;
        background:
            radial-gradient(circle at top right, rgba(34, 122, 173, 0.12), transparent 36%),
            radial-gradient(circle at bottom left, rgba(61, 153, 88, 0.1), transparent 32%),
            var(--color-bg);
        color: var(--color-text);
    }

    .booking-shell {
        width: 100%;
        max-width: 480px;
        min-height: 100vh;
        margin: 0 auto;
        padding: 16px 14px 28px;
    }

    .card {
        background: var(--color-card);
        border: 1px solid var(--color-border);
        border-radius: 14px;
        padding: 16px;
        box-shadow: var(--shadow-soft);
        margin-bottom: 14px;
    }

    .clinic-head {
        display: grid;
        grid-template-columns: 62px 1fr;
        gap: 12px;
        align-items: center;
    }

    .clinic-logo-wrap {
        width: 62px;
        height: 62px;
        border-radius: 14px;
        background: var(--color-primary-soft);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        border: 1px solid rgba(27, 110, 143, 0.2);
    }

    .clinic-logo {
        max-width: 100%;
        max-height: 100%;
        object-fit: cover;
    }

    .clinic-head h1 {
        font-family: "Tajawal", sans-serif;
        margin: 0;
        font-size: 21px;
        font-weight: 600;
        line-height: 1.35;
    }

    .clinic-head p {
        margin: 4px 0 0;
        color: var(--color-muted);
        font-size: 14px;
    }

    .day-box {
        text-align: center;
        border-top: 4px solid var(--color-primary);
    }

    .day-box h2,
    .card h3 {
        margin: 0 0 10px;
        font-size: 17px;
    }

    .booking-date {
        font-family: "Tajawal", sans-serif;
        font-size: 24px;
        font-weight: 500;
        margin-bottom: 10px;
    }

    .message-box {
        border-radius: 12px;
        padding: 12px;
        margin-bottom: 14px;
        font-size: 14px;
        line-height: 1.65;
        border: 1px solid transparent;
    }

    .message-box.success {
        background: #eaf8ef;
        border-color: #b3dfc1;
        color: #1a6d3f;
    }

    .message-box.error {
        background: #fdeeee;
        border-color: #f0c2c2;
        color: #9f2323;
    }

    .field-group {
        margin-bottom: 9px;
    }

    label {
        display: block;
        margin-bottom: 4px;
        font-size: 14px;
    }

    input:not([type="radio"]) {
        width: 100%;
        border: 1px solid var(--color-border);
        border-radius: 10px;
        padding: 10px 12px;
        font-size: 16px;
        font-family: inherit;
        background: #fff;
        touch-action: manipulation;
    }

    input:not([type="radio"]):focus {
        outline: none;
        border-color: var(--color-primary);
        box-shadow: 0 0 0 3px rgba(27, 110, 143, 0.13);
    }

    .visit-type-options {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
    }

    .visit-type-option {
        border: 1px solid var(--color-border);
        border-radius: 10px;
        padding: 10px 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        cursor: pointer;
        background: #fff;
        font-weight: 600;
        margin: 0;
    }

    .visit-type-option:has(input:checked) {
        border-color: var(--color-primary);
        background: var(--color-primary-soft);
        color: var(--color-primary);
    }

    .visit-type-option.disabled {
        opacity: .55;
        cursor: not-allowed;
        background: #f8fafc;
    }

    .visit-type-notice {
        border: 1px solid #f1d7a8;
        background: #fff9ed;
        color: #855a16;
        border-radius: 10px;
        padding: 10px 12px;
        margin-bottom: 12px;
        font-size: 13px;
        line-height: 1.6;
    }

    .field-error {
        display: block;
        color: var(--color-danger);
        min-height: 18px;
        margin-top: 4px;
        font-size: 12px;
    }

    .btn-primary {
        width: 100%;
        min-height: 50px;
        border: 0;
        border-radius: 11px;
        background: linear-gradient(135deg, #1b6e8f, #155877);
        color: #fff;
        font-size: 18px;
        font-family: "Tajawal", sans-serif;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        touch-action: manipulation;
    }

    .btn-primary[disabled] {
        opacity: 0.75;
        cursor: not-allowed;
    }

    .btn-spinner {
        width: 16px;
        height: 16px;
        border: 2px solid rgba(255, 255, 255, 0.4);
        border-top-color: #fff;
        border-radius: 50%;
        display: none;
        animation: spin .8s linear infinite;
    }

    .btn-primary.loading .btn-spinner {
        display: inline-block;
    }

    .closed-box {
        border: 2px solid #f4d2a8;
        background: #fff8ef;
        text-align: center;
    }

    .closed-box h3 {
        margin-bottom: 6px;
    }

    .closed-box p {
        margin: 0;
        color: #8d5518;
    }

    .next-open-wrap {
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px dashed #e7c79e;
        color: #7f4d14;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .next-open-label {
        font-size: 13px;
        color: #9a6a35;
    }

    #nextOpenText {
        font-size: 16px;
        font-weight: 700;
    }

    .my-booking-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        padding: 10px 0;
        border-bottom: 1px solid var(--color-border);
    }

    .my-booking-item:last-child {
        border-bottom: 0;
    }

    .cancel-btn {
        border: 1px solid var(--color-danger);
        background: transparent;
        color: var(--color-danger);
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 13px;
        cursor: pointer;
        touch-action: manipulation;
    }

    .fade-update {
        animation: fadeUpdate .45s ease;
    }

    @keyframes fadeUpdate {
        from { opacity: .4; transform: translateY(2px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }
</style>
