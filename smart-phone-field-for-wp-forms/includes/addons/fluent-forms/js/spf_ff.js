class PCAFE_SPF_FF {
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

        if (this.options.config) {
            comOps.initialCountry = this.global.spf_default_country;
            comOps.geoIpLookup = this.global.spf_geoip ? 1 : 0;
            comOps.validation = this.global.spf_frontend_validation ? 1 : 0;
        }

        if (this.options.config == '' && this.options.geoIp) {
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
            autoHideDialCode: true,
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

        let errorMsg = input.parentNode.parentNode.querySelector(".error-msg"),
            validMsg = input.parentNode.parentNode.querySelector(".valid-msg");

        if (input.value) {
            if (isValid) {
                errorMsg.classList.add('hide');
                validMsg.classList.remove('hide');
            } else {
                validMsg.classList.add('hide');
                errorMsg.classList.remove('hide');
            }
        } else {
            validMsg.classList.add('hide');
            errorMsg.classList.add('hide');
        }
    }

    formatValidation(input, iti) {
        if (!this.spf_config.validation) return;

        const isValid = iti.isValidNumber();

        let errorMsg = input.parentNode.parentNode.querySelector(".error-msg"),
            validMsg = input.parentNode.parentNode.querySelector(".valid-msg");

        if (input.value) {
            if (isValid) {
                errorMsg.classList.add('hide');
                validMsg.classList.remove('hide');
            } else {
                validMsg.classList.add('hide');
                errorMsg.classList.add('hide');
            }
        } else {
            validMsg.classList.add('hide');
            errorMsg.classList.add('hide');
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