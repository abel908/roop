import { track } from './analytics';
import { checkFieldValidity } from './validated-form';

/*
 * Submit a Project — three steps, progress bar, real-time checks in the
 * browser, summary before sending (§6.3). The server validates everything again.
 */
export function submissionForm(config) {
    return {
        step: config.initialStep || 1,
        total: 3,
        maxFiles: config.maxFiles,
        maxBytes: config.maxKb * 1024,
        extensions: config.extensions,
        descriptionMax: config.descriptionMax,
        files: [],
        fileErrors: [],
        errors: {},
        submitting: false,
        data: config.old || {},

        init() {
            this.$nextTick(() => this.collect());
        },

        get progress() {
            return Math.round((this.step / this.total) * 100);
        },

        fieldsOf(step) {
            return [...this.$root.querySelectorAll(`[data-step="${step}"] [name]`)]
                .filter((el) => el.name !== 'documents[]' && el.type !== 'hidden');
        },

        checkField(el) {
            return checkFieldValidity(el, config.messages, this.errors);
        },

        validateStep(step) {
            const fields = this.fieldsOf(step);
            const valid = fields.map((el) => this.checkField(el)).every(Boolean);
            if (!valid) {
                const first = fields.find((el) => this.errors[el.name]);
                first?.focus();
            }
            return valid && (step !== 3 || this.fileErrors.length === 0);
        },

        next() {
            if (!this.validateStep(this.step)) return;
            this.step = Math.min(this.total, this.step + 1);
            this.collect();
            this.scrollTop();
        },

        previous() {
            this.step = Math.max(1, this.step - 1);
            this.scrollTop();
        },

        scrollTop() {
            this.$nextTick(() => {
                this.$refs.top.scrollIntoView({ behavior: 'smooth', block: 'start' });
                this.$refs.stepHeading?.focus({ preventScroll: true });
            });
        },

        collect() {
            const form = this.$root.querySelector('form');
            const data = {};
            new FormData(form).forEach((value, key) => {
                if (typeof value === 'string') data[key] = value;
            });
            form.querySelectorAll('select').forEach((select) => {
                data[`${select.name}__label`] = select.selectedOptions[0]?.textContent?.trim() ?? '';
            });
            this.data = data;
        },

        label(name) {
            return this.data[`${name}__label`] || this.data[name] || '—';
        },

        addFiles(event) {
            const incoming = [...event.target.files];
            this.fileErrors = [];
            const accepted = [...this.files];

            incoming.forEach((file) => {
                const ext = file.name.split('.').pop().toLowerCase();
                if (!this.extensions.includes(ext)) {
                    this.fileErrors.push(`${file.name} — ${config.messages.fileType}`);
                } else if (file.size > this.maxBytes) {
                    this.fileErrors.push(`${file.name} — ${config.messages.fileSize}`);
                } else if (accepted.length >= this.maxFiles) {
                    this.fileErrors.push(`${file.name} — ${config.messages.fileCount}`);
                } else if (!accepted.some((f) => f.name === file.name && f.size === file.size)) {
                    accepted.push(file);
                    track('file_upload', { file_type: ext });
                }
            });

            this.files = accepted;
            this.syncInput();
        },

        removeFile(index) {
            this.files.splice(index, 1);
            this.fileErrors = [];
            this.syncInput();
        },

        syncInput() {
            const transfer = new DataTransfer();
            this.files.forEach((file) => transfer.items.add(file));
            this.$refs.fileInput.files = transfer.files;
        },

        size(bytes) {
            return bytes > 1048576 ? `${(bytes / 1048576).toFixed(1)} MB` : `${Math.max(1, Math.round(bytes / 1024))} KB`;
        },

        submit(event) {
            if (![1, 2, 3].every((s) => this.validateStep(s))) {
                event.preventDefault();
                const firstInvalid = [1, 2, 3].find((s) => this.fieldsOf(s).some((el) => this.errors[el.name]));
                if (firstInvalid) this.step = firstInvalid;
                return;
            }
            this.submitting = true;
        },
    };
}
