class PCAFE_SPF_Formidable {
    constructor(options, globalOptions) {
        this.options = options;
        this.global = globalOptions;
        this.init();
        this.spf_config;
    }
    init() {
        this.combineOptions();
        this.initSmartPhoneField();
    }
    combineOptions() {
        let comOps = this.options;

        if (this.options.config == 'global') {
            comOps.initialCountry = this.global.spf_default_country;
            comOps.geoIpLookup = this.global.spf_geoip ? 1 : 0;
            comOps.validation = this.global.spf_frontend_validation ? 1 : 0;
        }

        if (this.options.config == 'custom') {
            comOps.geoIpLookup = this.options.geoIp ? 1 : 0;
        }

        comOps.countrySearch = this.global.spf_country_search ? 1 : 0;
        comOps.dropdwonCoutnries = this.global.spf_restrict_country ? this.global.spf_restrict_country : '';
        comOps.restrictType = this.global.spf_restrict_type;
        comOps.ipinfoToken = this.global.spf_ipinfo_token;

        this.spf_config = comOps;
    }
    initSmartPhoneField() {
        if (typeof intlTelInput == 'undefined') {
            return;
        }

        const input = this.options.inputId;
        const iti = window.intlTelInput(input, this.configuration());

        input.addEventListener('keypress', function (e) {
            var charCode = e.which ? e.which : e.keyCode;
            if (String.fromCharCode(charCode).match(/[^0-9+]/g)) {
                e.preventDefault();
            }
        });

        this.addCountryCodeInputHandler(input, iti);

        input.addEventListener('blur', (e) => {
            this.validateNumber(input, iti);
        });

        input.addEventListener('keyup', (e) => {
            this.formatValidation(input, iti);
        });
    }

    configuration() {
        let config = {
            initialCountry: this.spf_config.initialCountry,
            formatOnDisplay: false,
            countrySearch: this.spf_config.countrySearch ? true : false,
            fixDropdownWidth: true,
            nationalMode: false,
            formatAsYouType: false,
            useFullscreenPopup: false,
            autoHideDialCode: false
        };

        if (this.spf_config.restrictType == 'exclude') {
            config.excludeCountries = this.spf_config.dropdwonCoutnries;
        }

        if (this.spf_config.restrictType == 'include') {
            config.onlyCountries = this.spf_config.dropdwonCoutnries;
        }

        if (this.spf_config.geoIpLookup || this.spf_config.initialCountry == 'auto') {
            var defaultCountry = this.spf_config.initialCountry.toString().toLowerCase();
            var api_url = this.spf_config.ipinfoToken ? `https://ipinfo.io?token=${this.spf_config.ipinfoToken}` : "https://ipinfo.io/json";
            config.initialCountry = 'auto';
            config.geoIpLookup = function (callback) {
                fetch(api_url)
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

    validateNumber(input, iti) {
        if (!this.spf_config.validation) return;
        const isValid = iti.isValidNumber();

        if (input.value) {
            if (isValid) {
                input.classList.remove('frm_invalid');
                input.classList.add('frm_valid');
            } else {
                input.classList.remove('frm_valid');
                input.classList.add('frm_invalid');
            }
        } else {
            input.classList.remove('frm_valid');
            input.classList.remove('frm_invalid');
        }
    }

    formatValidation(input, iti) {
        if (!this.spf_config.validation) return;

        const isValid = iti.isValidNumber();

        if (input.value) {
            if (isValid) {
                input.classList.remove('frm_invalid');
                input.classList.add('frm_valid');
            } else {
                input.classList.remove('frm_valid');
                input.classList.remove('frm_invalid');
            }
        } else {
            input.classList.remove('frm_valid');
            input.classList.remove('frm_invalid');
        }
    }

    addCountryCodeInputHandler(inputElement, iti) {
        const handleCountryChange = (event) => {

            const currentCountryData = iti.getSelectedCountryData();
            const currentCode = `+${currentCountryData.dialCode}`;

            this.updateCountryCodeHandler(event.currentTarget, currentCode);
        }

        inputElement.addEventListener('keydown', handleCountryChange);
        inputElement.addEventListener('input', handleCountryChange);
        inputElement.addEventListener('countrychange', handleCountryChange);
    }

    updateCountryCodeHandler(input, currentCode) {
        let value = input.value;

        if (currentCode && '+undefined' === currentCode || ['', '+'].includes(value)) {
            return;
        }

        if (!value.startsWith(currentCode)) {
            value = value.replace(/\+/g, '');
            input.value = currentCode + value;
        }
    }
}

function initFormidableSPFFields() {
    document.querySelectorAll('.frm_spf_input').forEach(function (input) {

        let globalOptions = typeof pcafe_spf_global_setting !== 'undefined' ? pcafe_spf_global_setting : {};

        let options = {
            inputId: input,
            config: input.getAttribute('data-config') || 'global',
            initialCountry: input.getAttribute('data-init_country') || 'us',
            validation: input.getAttribute('data-validation') || 0,
            geoIp: input.getAttribute('data-geoip') || 0,
        };

        console.log(options);

        new PCAFE_SPF_Formidable(options, globalOptions);
    });
}

// Run on page load
document.addEventListener("DOMContentLoaded", function() {
    initFormidableSPFFields();
});

// Run again when Formidable Forms does ajax loading (if applicable)
jQuery(document).ajaxComplete(function(event, xhr, settings) {
    if (settings.action === 'frm_entries_ajax_submit' || settings.data && settings.data.indexOf('action=frm_entries_ajax_submit') !== -1) {
        initFormidableSPFFields();
    }
});
