import { track } from './analytics';

/*
 * Real-time validation in the browser for the contact and interest forms
 * (§6.3, §12.3): messages associated with each field, focus on the first
 * error. The server always validates again.
 */
export function checkFieldValidity(el, messages, errors) {
    let message = '';
    const value = (el.value ?? '').trim();

    if (el.type === 'checkbox') {
        if (el.required && !el.checked) message = messages.accepted;
    } else if (el.required && !value) {
        message = messages.required;
    } else if (el.type === 'email' && value && !/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(value)) {
        message = messages.email;
    } else if (el.type === 'tel' && value && !/^\+?[0-9 ().-]{6,30}$/.test(value)) {
        message = messages.phone;
    } else if (el.minLength > 0 && value && value.length < el.minLength) {
        message = messages.min.replace(':min', el.minLength);
    }

    if (message) {
        errors[el.name] = message;
        el.setAttribute('aria-invalid', 'true');
        el.classList.remove('is-valid');
    } else {
        delete errors[el.name];
        el.removeAttribute('aria-invalid');
        if (value && el.type !== 'checkbox') el.classList.add('is-valid');
    }

    return !message;
}

export function validatedForm(messages, event = null) {
    return {
        errors: {},
        submitting: false,
        checkField(el) {
            return checkFieldValidity(el, messages, this.errors);
        },
        submit(e) {
            const fields = [...e.target.querySelectorAll('[name]')].filter((el) => el.type !== 'hidden' && !el.closest('.hp-field'));
            const valid = fields.map((el) => this.checkField(el)).every(Boolean);
            if (!valid) {
                e.preventDefault();
                fields.find((el) => this.errors[el.name])?.focus();
                return;
            }
            this.submitting = true;
            if (event) track(event);
        },
    };
}
