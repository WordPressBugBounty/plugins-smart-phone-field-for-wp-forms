class PCAFE_SPF_NinjaForms {
    constructor(options, globalOptions) {
        this.options = options;
        this.global = globalOptions || {};
        this.spf_config = {};
        this.init();
    }

    init() {
        this.combineOptions();
        this.initSmartPhoneField();
    }

    combineOptions() {
        let comOps = this.options;

        if (this.options.config === 'global') {
            comOps.initialCountry = this.global.spf_default_country || 'us';
            comOps.geoIpLookup = this.global.spf_geoip ? 1 : 0;
            comOps.validation = this.global.spf_frontend_validation ? 1 : 0;
        }

        if (this.options.config === 'custom') {
            comOps.geoIpLookup = this.options.geoIp ? 1 : 0;
        }

        comOps.countrySearch = this.global.spf_country_search ? 1 : 0;
        comOps.dropdwonCoutnries = this.global.spf_restrict_country ? this.global.spf_restrict_country : '';
        comOps.restrictType = this.global.spf_restrict_type;
        comOps.ipinfoToken = this.global.spf_ipinfo_token;

        this.spf_config = comOps;
    }

    initSmartPhoneField() {
        if (typeof intlTelInput === 'undefined') {
            return;
        }

        const input = this.options.input;

        if (!input || input.dataset.spfNinjaFormsInitialized) {
            return;
        }

        input.dataset.spfNinjaFormsInitialized = '1';

        const iti = window.intlTelInput(input, this.configuration());

        input.addEventListener('keypress', function (e) {
            const charCode = e.which ? e.which : e.keyCode;
            if (String.fromCharCode(charCode).match(/[^0-9+]/g)) {
                e.preventDefault();
            }
        });

        this.addCountryCodeInputHandler(input, iti);

        input.addEventListener('blur', () => {
            this.validateNumber(input, iti);
        });

        input.addEventListener('keyup', () => {
            this.formatValidation(input, iti);
        });
    }

    configuration() {
        let config = {
            initialCountry: this.spf_config.initialCountry,
            containerClass: 'pcafe_spf_ninja_forms',
            formatOnDisplay: false,
            countrySearch: this.spf_config.countrySearch ? true : false,
            fixDropdownWidth: true,
            nationalMode: false,
            autoHideDialCode: false,
            formatAsYouType: false,
            useFullscreenPopup: false
        };

        if (this.spf_config.restrictType === 'exclude') {
            config.excludeCountries = this.spf_config.dropdwonCoutnries;
        }

        if (this.spf_config.restrictType === 'include') {
            config.onlyCountries = this.spf_config.dropdwonCoutnries;
        }

        if (this.spf_config.geoIpLookup || this.spf_config.initialCountry === 'auto') {
            const defaultCountry = this.spf_config.initialCountry.toString().toLowerCase();
            const apiUrl = this.spf_config.ipinfoToken ? `https://ipinfo.io?token=${this.spf_config.ipinfoToken}` : 'https://ipinfo.io/json';

            config.initialCountry = 'auto';
            config.geoIpLookup = function (callback) {
                fetch(apiUrl)
                    .then(r => r.json())
                    .then(data => {
                        const country = (data && data.country) ? data.country.toLowerCase() : defaultCountry;
                        callback(country);
                    })
                    .catch(() => callback(defaultCountry));
            };
        }

        return config;
    }

    addCountryCodeInputHandler(inputElement, iti) {
        const handleCountryChange = (event) => {
            const currentCountryData = iti.getSelectedCountryData();
            const currentCode = `+${currentCountryData.dialCode}`;

            this.updateCountryCodeHandler(event.currentTarget, currentCode);
        };

        inputElement.addEventListener('keydown', handleCountryChange);
        inputElement.addEventListener('input', handleCountryChange);
        inputElement.addEventListener('countrychange', handleCountryChange);
    }

    updateCountryCodeHandler(input, currentCode) {
        let value = input.value;

        if ((currentCode && '+undefined' === currentCode) || ['', '+'].includes(value)) {
            return;
        }

        if (!value.startsWith(currentCode)) {
            value = value.replace(/\+/g, '');
            input.value = currentCode + value;
            input.dispatchEvent(new Event('change', { bubbles: true }));
        }
    }

    hasValidationUtils(iti) {
        return !!(
            iti &&
            typeof iti.isValidNumber === 'function' &&
            window.intlTelInput &&
            window.intlTelInput.utils
        );
    }

    isValidNumber(iti) {
        try {
            return iti.isValidNumber();
        } catch (e) {
            return null;
        }
    }

    validateNumber(input, iti) {
        if (!this.spf_config.validation || !this.hasValidationUtils(iti)) {
            input.classList.remove('valid');
            input.classList.remove('invalid');
            return;
        }

        const isValid = this.isValidNumber(iti);

        if (isValid === null) {
            input.classList.remove('valid');
            input.classList.remove('invalid');
            return;
        }

        if (input.value) {
            if (isValid) {
                input.classList.remove('invalid');
                input.classList.add('valid');
            } else {
                input.classList.remove('valid');
                input.classList.add('invalid');
            }
        } else {
            input.classList.remove('valid');
            input.classList.remove('invalid');
        }
    }

    formatValidation(input, iti) {
        if (!this.spf_config.validation || !this.hasValidationUtils(iti)) {
            input.classList.remove('valid');
            input.classList.remove('invalid');
            return;
        }

        const isValid = this.isValidNumber(iti);

        if (isValid === null) {
            input.classList.remove('valid');
            input.classList.remove('invalid');
            return;
        }

        if (input.value) {
            if (isValid) {
                input.classList.remove('invalid');
                input.classList.add('valid');
            } else {
                input.classList.remove('valid');
                input.classList.remove('invalid');
            }
        } else {
            input.classList.remove('valid');
            input.classList.remove('invalid');
        }
    }
}

function initPcafeSpfNinjaFormsFields() {
    document.querySelectorAll('.pcafe_spf_ninja_forms_input').forEach(function (input) {
        const globalOptions = typeof pcafe_spf_global_setting !== 'undefined' ? pcafe_spf_global_setting : {};

        const options = {
            input: input,
            config: input.getAttribute('data-config') || 'global',
            initialCountry: input.getAttribute('data-init_country') || 'us',
            validation: input.getAttribute('data-validation') || 0,
            geoIp: input.getAttribute('data-geoip') || 0
        };

        new PCAFE_SPF_NinjaForms(options, globalOptions);
    });
}

document.addEventListener('DOMContentLoaded', initPcafeSpfNinjaFormsFields);

jQuery(document).on('nfFormReady', initPcafeSpfNinjaFormsFields);
