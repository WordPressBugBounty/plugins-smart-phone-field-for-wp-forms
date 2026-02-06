class PCAFE_SPF_WPFORMS {
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

        if (this.options.config == 'custom' && this.options.geoIp) {
            comOps.initialCountry = 'auto';
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

        const input = document.querySelector('#' + this.options.inputId);
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
            autoHideDialCode: false,
            formatAsYouType: false,
            useFullscreenPopup: false
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

    validateNumber(input, iti) {
        if (!this.spf_config.validation) return;
        const isValid = iti.isValidNumber();

        console.log(iti);

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
        if (!this.spf_config.validation) return;

        const isValid = iti.isValidNumber();

        if (input.value) {
            if (isValid) {
                input.classList.remove('invalid');
                input.classList.add('valid');
            }
        } else {
            input.classList.remove('valid');
            input.classList.remove('invalid');
        }
    }
}

document.querySelectorAll('.pcafe_sphone_field').forEach(function (input) {

    let globalOptions = pcafe_spf_global_setting;

    let options = {
        inputId: input.getAttribute('id'),
        config: input.getAttribute('data-config'),
        geoIp: input.getAttribute('data-geoip') ? input.getAttribute('data-geoip') : 0,
        initialCountry: input.getAttribute('data-dc') ? input.getAttribute('data-dc') : 'us',
        validation: input.getAttribute('data-validation') ? input.getAttribute('data-validation') : 0
    };

    new PCAFE_SPF_WPFORMS(options, globalOptions);
});